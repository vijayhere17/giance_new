@extends('users.master')
@section('extra')
@endsection
@section('content')
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('/') }}/dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="javascript:">Withdrawal</a></li>
                            <li class="breadcrumb-item" aria-current="page">Withdrawal History</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Withdrawal History</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <h5 class="mb-0">Withdrawal History</h5>
                        <div class="btn-group" role="group" aria-label="Status filter">
                            <button type="button" class="btn btn-sm btn-outline-secondary status-filter active" data-status="">All</button>
                            <button type="button" class="btn btn-sm btn-outline-warning status-filter" data-status="pending">Pending</button>
                            <button type="button" class="btn btn-sm btn-outline-success status-filter" data-status="approved">Approved</button>
                            <button type="button" class="btn btn-sm btn-outline-danger status-filter" data-status="rejected">Rejected</button>
                        </div>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table" id="tableList">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Request On</th>
                                        <th>Income Type</th>
                                        <th>Amount</th>
                                        <th>Admin Charge</th>
                                        <th>Net Amount</th>
                                        <th>Wallet</th>
                                        <th>Txn. Hash</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('jscontent')
<script src="{{ URL::to('/') }}/assets/js/users/withdrawal-request.0.10.js?v=1"></script>
@endsection
