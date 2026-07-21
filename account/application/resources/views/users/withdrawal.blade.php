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
                            <li class="breadcrumb-item" aria-current="page">{{ $page_titel }}</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">{{ $page_titel }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <div class="row">
            <!-- [ form-element ] start -->
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ $page_titel }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <form>
                                <div class="row g-4">
                                    
                                    <div class="col-md-12">
                                        <div class="card bg-primary available-balance-card">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div>
                                                        <p class="mb-0 text-white text-opacity-75">Total Wallet Balance ($)</p>
                                                        <h4 class="mb-0 text-white balance">{{ $balance }}</h4>
                                                    </div>
                                                    <div class="avtar">
                                                        <i class="ti ti-arrows-left-right f-18"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group mb-0">
                                            <label class="form-label" for="income_type">Select Withdrawal Type</label>
                                            <select class="form-control" name="income_type" id="income_type" style="height: 58px;">
                                                <option value="">-- Select --</option>
                                                @foreach(($income_options ?? []) as $opt)
                                                    <option value="{{ $opt['id'] }}"
                                                        data-balance="{{ $opt['balance'] }}"
                                                        data-zero-fee="{{ !empty($opt['zero_fee']) ? 1 : 0 }}">
                                                        {{ $opt['name'] }} (Avail: ${{ number_format($opt['balance'], 4) }}){{ !empty($opt['zero_fee']) ? ' — No Deduction' : ' — With Deduction' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <small class="text-muted income-type-hint">Only 2 options: <b>Locked Reward Unlock</b> (no deduction) or <b>Other Incomes</b> total (with deduction).</small>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="alert alert-info py-2 mb-0 income-balance-box" style="display:none;">
                                            Selected income available: $<span class="type-balance">0.0000</span>
                                            &nbsp;|&nbsp; Charge: <span class="charge-label">—</span>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <x-input type="text" name="amount" id="amount" placeholder="Withdrawal amount ($)" value=""/>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <x-input type="text" name="admin_charge" id="admin_charge" placeholder="Admin charge ($)" value=""/>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <x-input type="text" name="net_amount" id="net_amount" placeholder="Net amount ($)" value=""/>
                                    </div>
                                    
                                    <div class="col-md-4" style="display: none;">
                                        <x-input type="text" name="coin_rate" id="coin_rate" placeholder="Withdrawal rate ($)" value=""/>
                                    </div>
                                    
                                    <div class="col-md-4"  style="display: none;">
                                        <x-input type="text" name="usd_amount" id="usd_amount" placeholder="Withdrawal amount (USDT BEP20)" value=""/>
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <x-input type="text" name="with_wallet" id="with_wallet" placeholder="Withdrawal wallet address" value=""/>
                                    </div>
                                    
                                    <div class="col-md-12" style="display: none;">
                                        <x-input type="text" name="otp" id="otp" placeholder="One - Time Password" value=""/>
                                    </div>
                                </div>
                            </form>
                        </div>    
                    </div>
                    <div class="card-footer">
                        <center>
                            <button type="submit" class="btn btn-warning btn-otp-submit" style="width: 100%; display: none; ">Get OTP</button>
                            <button type="submit" class="btn btn-primary btn-submit" style="width: 100%;">Submit</button>
                        </center>
                    </div>   
                </div>
            </div>  
            <div class="col-md-2"></div>  
        </div>
    </div>
</div>
@endsection
@section('jscontent')
<script src="{{ URL::to('/') }}/assets/js/users/withdrawal.0.13.js?v=2"></script>
@endsection
