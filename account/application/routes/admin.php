<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes — current ROI investment plan only
| Legacy MLM routes (binary/DMC/bonanza/sunday/cashback/travel/swap) removed from active map.
|--------------------------------------------------------------------------
*/

Route::controller(App\Http\Controllers\Admin\LoginController::class)->group(function()
{
    Route::get('login', 'index')->name('admin.login');
    Route::post('process-admin-login', 'login');
});

Route::middleware('adminauth:admin')->group(function () {

    Route::get('home', [\App\Http\Controllers\Admin\DashboardController::class,'index']);

    Route::controller(App\Http\Controllers\Admin\CommonController::class)->group(function()
    {
        Route::get('change-password', 'cpassword');
        Route::post('process-change-password', "changePassword");

        Route::get('coin-rate-set', 'coinrateset');
        Route::post('process-update-coin-rate', "changeCoinRate");
    });

    Route::controller(App\Http\Controllers\Admin\StakeReportController::class)->group(function()
    {
        Route::get('stake-request/{status}/{title}', 'stakeReport');
        Route::get('get-stake-report', 'getStakeRequest');

        Route::get('user-staked-report', 'userStakedReport');
        Route::get('get-staked-report', 'getStakedReport');

        Route::get('manual-topup', 'newTopup');
        Route::post('process-manual-topup', "adminStakeIDs");

        Route::get('new-package', 'newAddPack');
        Route::post('process-new-package', "addPackage");

        // Capital withdrawal ops
        Route::get('user-staked-withdrawal', 'userStakedWithdrawal');
        Route::get('get-staked-withdrawal', 'getStakedWithdrawalReport');
        Route::post('process-staked-withdrawal-req', "actionCapitalWithdraw");

        Route::get('all-packages', 'allPackages');
        Route::get('get-all-packages-report', 'getAllPackagesReport');

        Route::post('update-package/{id}', 'update');
        Route::post('add-new-package', 'store');
    });

    Route::controller(App\Http\Controllers\Admin\MemberController::class)->group(function()
    {
        Route::get('member-report', 'index');
        Route::get('get-member-report', 'getMemberReport');
        Route::post('process-back-login', 'backLogin');

        Route::get('edit-{id}-profile', 'editmember');
        Route::post('process-update-member', 'updateMemberDetails');
    });

    Route::controller(App\Http\Controllers\Admin\EarningReportController::class)->group(function()
    {
        Route::get('cradit-debit-master', 'craditdebitMaster');
        Route::post('process-cradit-debit-master', 'actionCraditDebit');

        Route::get('cradit-debit-report', 'craditdebitReport');
        Route::get('get-cradit-debit-report', 'getCraditDebitReport');

        Route::get('outstanding-balance', 'balanceReport');
        Route::get('get-balance-report', 'getBalanceReport');

        // Plan incomes only: 1 Direct, 2 ROI, 4 Team Level, 7 Reward Salary, 8 Booster, 10 Bonus Income
        Route::get('earning-report/{status}/{title}', 'earningReport');
        Route::get('get-earning-report', 'getEarningReport');
    });

    Route::controller(App\Http\Controllers\Admin\RoiTierController::class)->group(function()
    {
        Route::get('roi-tier-master', 'index');
        Route::post('add-roi-tier', 'addTier');
        Route::post('update-roi-tier/{id}', 'updateTier');
    });

    Route::controller(App\Http\Controllers\Admin\TurnoverRewardController::class)->group(function()
    {
        Route::get('turnover-reward-master', 'index');
        Route::post('add-turnover-reward', 'addReward');
        Route::post('update-turnover-reward/{id}', 'updateReward');

        Route::get('turnover-reward-achievers', 'indexAchievers');
        Route::get('reward-weekly-salary', 'indexWeeklySalary');
        Route::get('locked-reward-report', 'indexLockedReward');
    });

    Route::controller(App\Http\Controllers\Admin\WithdrawalController::class)->group(function()
    {
        Route::get('withdrawal-request/{status}/{title}', 'indexReport');
        Route::get('get-withdrawal-report', 'getWithdrawRequest');

        Route::post('process-withdrawal-request', "withdrawalReqAction");
        Route::post('process-manual-withdrawal-request', "withdrawalReqActionManual");
    });

    Route::controller(App\Http\Controllers\Admin\TicketController::class)->group(function()
    {
        Route::get('support-ticket', 'index');

        Route::get('get-all-support-ticket', 'getAllSupportTicket');

        Route::post('process-view-ticket-message', "AdminViewTicketMessage");

        Route::post('process-send-ticket-message', "AdminSendTicketMessage");
    });

    Route::get('logout', function () {
        Auth::guard('admin')->logout();
        return redirect('/admin/login');
    });
});
