<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use App\Models\WithdrawalLog;
use App\Models\User;
use Log;
use DB;

class WithdrawalController extends Controller
{
    // Unified Withdrawal Request Management page (filterable)
    public function indexManagement()
    {
        $page_titel = 'Withdrawal Request Management';
        return view('admin.withdrawal-request')->with([
            'page_titel' => $page_titel,
            'status' => '',
        ])->toJS();
    }

    // Legacy status-specific URLs redirect into the management page filter
    public function indexReport($status, $title)
    {
        $page_titel = 'Withdrawal Request Management';
        return view('admin.withdrawal-request')->with([
            'page_titel' => $page_titel,
            'status' => (string) $status,
        ])->toJS();
    }

    public function getWithdrawRequest(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $length = $request->get("length");

        $status_filter = strtolower((string) $request->get('status_filter', ''));
        // Fallback for legacy pages that pass numeric status without status_filter
        if ($status_filter === '' && $request->filled('status') && $request->get('status') !== 'all') {
            $legacy = (string) $request->get('status');
            if ($legacy === '0' || $legacy === '1') {
                $status_filter = 'pending';
            } elseif ($legacy === '2') {
                $status_filter = 'approved';
            } elseif ($legacy === '3') {
                $status_filter = 'rejected';
            }
        }

        $search_arr = $request->get('search');
        $searchValue = $search_arr['value'] ?? null;

        // Only earning withdrawals for the current business plan
        $listwithdrawreq = WithdrawalLog::join('users', 'withdrawal_requests.member_id', '=', 'users.id')
            ->where('withdrawal_requests.mode', 0)
            ->orderBy('withdrawal_requests.created_at', 'desc');

        if ($status_filter === 'pending') {
            $listwithdrawreq->whereIn('withdrawal_requests.status', [0, 1]);
        } elseif ($status_filter === 'approved') {
            $listwithdrawreq->where('withdrawal_requests.status', 2);
        } elseif ($status_filter === 'rejected') {
            $listwithdrawreq->where('withdrawal_requests.status', 3);
        }

        if ($searchValue != null) {
            $listwithdrawreq = $listwithdrawreq->where(function ($q) use ($searchValue) {
                $q->where('username', '=', $searchValue)
                  ->orWhere(DB::raw('CONCAT(firstname," ",lastname)'), 'like', '%'.$searchValue.'%')
                  ->orWhere('email', 'like', '%'.$searchValue.'%')
                  ->orWhere('mobile', 'like', '%'.$searchValue.'%')
                  ->orWhere('withdrawal_requests.address', 'like', '%'.$searchValue.'%');
            });
        }

        $totalRecords = $listwithdrawreq->count();
        $totalRecordswithFilter = $totalRecords;

        $records = $listwithdrawreq->select(
                'withdrawal_requests.id',
                'withdrawal_requests.ref_id',
                'withdrawal_requests.mode',
                'withdrawal_requests.w_type',
                'withdrawal_requests.charge_percent',
                'withdrawal_requests.member_id',
                'withdrawal_requests.amount',
                'withdrawal_requests.admin',
                'withdrawal_requests.tds',
                'withdrawal_requests.net',
                'withdrawal_requests.rate',
                'withdrawal_requests.payable',
                'withdrawal_requests.address',
                'withdrawal_requests.hash',
                'withdrawal_requests.remark',
                'withdrawal_requests.status',
                'withdrawal_requests.created_at'
            )
            ->with(['member' => function ($query) {
                $query->select('id', 'username', 'firstname', 'lastname');
            }])
            ->skip($start)
            ->take($length)
            ->get();

        $data_arr = [];
        $income_labels = config('income.withdrawal_buckets', [
            10 => 'Bonus Income',
            0 => 'Other Incomes',
        ]);

        foreach ($records as $record) {
            $income_name = $income_labels[$record->w_type] ?? ((int)$record->w_type === 10 ? 'Bonus Income' : 'Other Incomes');
            $w_mode = '<b>'.$income_name.'</b>';
            if ((int)$record->w_type === 10) {
                $w_mode .= '<br><small style="color:green;">0% fee</small>';
            } else {
                $fee = $record->charge_percent !== null ? $record->charge_percent : config('income.withdrawal_admin_fee_percent', 15);
                $w_mode .= '<br><small>Admin Fee: '.$fee.'%</small>';
            }

            $status_code = (int) $record->status;
            if ($status_code === 2) {
                $status_label = '<span class="label label-success">Approved</span>';
            } elseif ($status_code === 3) {
                $status_label = '<span class="label label-danger">Rejected</span>';
            } else {
                $status_label = '<span class="label label-warning">Pending</span>';
            }

            $data_arr[] = [
                "id" => $record->id,
                "request_on" => date("d/m/Y H:i A", strtotime($record->created_at)),
                "w_mode" => $w_mode,
                "username" => obscureAddress($record->member->username),
                "name" => $record->member->firstname.' '.$record->member->lastname,
                "amount" => $record->amount,
                "admin" => $record->admin,
                "net" => $record->net,
                "coin_rate" => $record->rate,
                "payable" => $record->payable,
                "wallet" => $record->address,
                "txn_hash" => $record->hash,
                "remark" => $record->remark,
                "status" => $status_code,
                "status_label" => $status_label,
            ];
        }

        return json_encode([
            "draw" => intval($draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordswithFilter,
            "data" => $data_arr,
        ]);
    }

    public function withdrawalReqAction(Request $request)
    {
        try {
            $v = Validator::make($request->all(), [
                'withdrawid' => 'required',
                'status' => 'required',
            ]);

            if ($v->fails()) {
                return response()->json(['success' => false, 'error_code' => 'INVALID_REQUEST_DATA'], 200);
            }

            $user = Auth::user();
            if ($user == null) {
                return response()->json(['success' => false, 'error_code' => 'SESSION_INVALID'], 200);
            }

            $walletCon = app('App\Http\Controllers\Users\EarningWalletController');
            $withdrawal = WithdrawalLog::find($request->withdrawid);

            if ($withdrawal == null) {
                return response()->json(['success' => false, 'error_code' => 'INVALID_REQUEST_DATA'], 200);
            }

            // Approve (manual) — status 2 or 5
            if ($request->status == 2 || $request->status == 5) {
                if (in_array((int)$withdrawal->status, [0, 1], true)) {
                    $withdrawal->status = 2;
                    if ($request->status == 5 && empty($withdrawal->remark)) {
                        $withdrawal->remark = 'Admin Approved';
                    }
                    $withdrawal->save();
                }
            }
            // Reject — refund wallet with same income bucket, status updates in member panel
            else if ($request->status == 3) {
                if (in_array((int)$withdrawal->status, [0, 1], true)) {
                    $debit_description = 'Withdrawal request has been rejected';
                    $earning_type = (int) $withdrawal->w_type;
                    $walletCon->addearningwalletlog(
                        $withdrawal->member_id,
                        1,
                        $earning_type,
                        $debit_description,
                        $withdrawal->amount,
                        $withdrawal->rate,
                        $withdrawal->payable,
                        date("Y-m-d H:i:s")
                    );

                    $withdrawal->status = 3;
                    $withdrawal->save();
                }
            }
            // Reset to pending
            else if ($request->status == 4) {
                if (in_array((int)$withdrawal->status, [1, 3], true)) {
                    // If reopening a rejected request, re-debit the refunded amount
                    if ((int)$withdrawal->status === 3) {
                        $walletCon->addearningwalletlog(
                            $withdrawal->member_id,
                            2,
                            (int) $withdrawal->w_type,
                            'Withdrawal request reopened',
                            $withdrawal->amount,
                            $withdrawal->rate,
                            $withdrawal->payable,
                            date("Y-m-d H:i:s")
                        );
                    }
                    $withdrawal->status = 0;
                    $withdrawal->save();
                }
            }

            return response()->json(['success' => true, 'error_code' => ''], 200);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['success' => false, 'error_code' => 'UNEXPECTED_ERROR_OCCURED'], 200);
        }
    }

    public function withdrawalReqActionManual(Request $request)
    {
        try {
            $v = Validator::make($request->all(), [
                'id' => 'required',
                'status' => 'required',
                'hash' => 'required',
            ]);

            if ($v->fails()) {
                return response()->json(['success' => false, 'error_code' => 'INVALID_REQUEST_DATA'], 200);
            }

            $user = Auth::user();
            if ($user == null) {
                return response()->json(['success' => false, 'error_code' => 'SESSION_INVALID'], 200);
            }

            $withdrawal = WithdrawalLog::find($request->id);
            if ($withdrawal == null) {
                return response()->json(['success' => false, 'error_code' => 'INVALID_REQUEST_DATA'], 200);
            }

            $withdrawal->status = $request->status;
            $withdrawal->hash = $request->hash;
            $withdrawal->save();

            return response()->json(['success' => true, 'error_code' => ''], 200);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['success' => false, 'error_code' => 'UNEXPECTED_ERROR_OCCURED'], 200);
        }
    }
}
