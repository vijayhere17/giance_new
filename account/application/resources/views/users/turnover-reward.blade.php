@extends('users.master')
@section('extra')
@endsection
@section('content')
<div class="pc-container">
    <div class="pc-content">
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

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Rewards Ratio — Progress</h5>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="row g-3 mt-0">
                            <div class="col-sm-3">
                                <div class="bg-body p-3 rounded">
                                    <p class="mb-0 text-muted">Direct Referrals</p>
                                    <h6 class="mb-0">{{ $progress['directs'] }}</h6>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="bg-body p-3 rounded">
                                    <p class="mb-0 text-muted">Team Size</p>
                                    <h6 class="mb-0">{{ $progress['team'] }}</h6>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="bg-body p-3 rounded">
                                    <p class="mb-0 text-muted">Self Investment</p>
                                    <h6 class="mb-0">${{ number_format($progress['self_business'], 2) }}</h6>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="bg-body p-3 rounded">
                                    <p class="mb-0 text-muted">Total Business</p>
                                    <h6 class="mb-0">${{ number_format($progress['total_business'] ?? (($progress['self_business'] ?? 0) + ($progress['team_business'] ?? 0)), 2) }}</h6>
                                </div>
                            </div>
                        </div>

                        @if($active_achiever)
                        <div class="alert alert-success mt-3 mb-0">
                            Active Weekly Salary: <strong>${{ number_format($active_achiever->weekly_salary, 2) }}</strong>
                            &nbsp;|&nbsp; Next Pay: <strong>{{ $active_achiever->return_date ? date('d/m/Y', strtotime($active_achiever->return_date)) : '-' }}</strong>
                            &nbsp;|&nbsp; Weeks Paid: <strong>{{ $active_achiever->weeks_paid }}/{{ $salary_weeks ?? 12 }}</strong>
                        </div>
                        @endif

                        <div class="table-responsive mt-4">
                            <table class="table" id="tableList">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Rank</th>
                                        <th>Direct</th>
                                        <th>Team</th>
                                        <th>Self Investment</th>
                                        <th>Total Business</th>
                                        <th>Salary / Week</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allrewards as $reward)
                                    @php
                                        $row = collect($achieved)->firstWhere('reward_id', $reward->id);
                                    @endphp
                                    <tr>
                                        <td>{{ $reward->milestone_order }}</td>
                                        <td><strong>{{ $reward->title ?: ('Rank '.$reward->milestone_order) }}</strong></td>
                                        <td>{{ $reward->required_directs }}</td>
                                        <td>{{ number_format($reward->required_team) }}</td>
                                        <td>${{ number_format($reward->required_self_business, 0) }}</td>
                                        <td>${{ number_format(($reward->required_team_business > 0 ? $reward->required_team_business : $reward->turnover_amount), 0) }}</td>
                                        <td>${{ number_format(($reward->weekly_salary > 0 ? $reward->weekly_salary : $reward->cash_reward), 0) }}</td>
                                        <td>{{ $salary_weeks ?? 12 }} Weeks</td>
                                        <td>
                                            @if($row == null)
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif((int)$row->status === 0)
                                                <span class="badge bg-success">Active</span>
                                            @elseif((int)$row->status === 2)
                                                <span class="badge bg-primary">Completed</span>
                                            @else
                                                <span class="badge bg-info">Achieved</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
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
@endsection
