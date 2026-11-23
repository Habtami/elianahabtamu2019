@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

@section('content')
<section>
    <div class="rad-body-wrapper rad-nav-min">
        <div class="container-fluid">
            <header class="rad-page-title">
                <span>Super Admin Dashboard</span>
                <small class="md-txt">Welcome {{ auth()->user()->name }}, <a href="#"><i class="fa fa-map-marker rad-txt-danger"></i> System Overview</a></small>
            </header>
            <div class="row">
                <div class="col-lg-3 col-xs-6">
                    <div class="rad-info-box rad-txt-success">
                        <i class="fa fa-users"></i>
                        <span class="heading">Total Users</span>
                        <span class="value"><span>{{ $totalUsers }}</span></span>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-6">
                    <div class="rad-info-box rad-txt-primary">
                        <i class="fa fa-user-check"></i>
                        <span class="heading">Active Users</span>
                        <span class="value"><span>{{ $activeUsers }}</span></span>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-6">
                    <div class="rad-info-box rad-txt-danger">
                        <i class="fa fa-user-shield"></i>
                        <span class="heading">Admin Users</span>
                        <span class="value"><span>{{ $adminUsers }}</span></span>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-6">
                    <div class="rad-info-box">
                        <i class="fa fa-file-contract"></i>
                        <span class="heading">Total Contracts</span>
                        <span class="value"><span>{{ $totalContracts }}</span></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-xs-6">
                    <div class="rad-info-box rad-txt-success">
                        <i class="fa fa-check-circle"></i>
                        <span class="heading">Active Contracts</span>
                        <span class="value"><span>{{ $activeContracts }}</span></span>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-6">
                    <div class="rad-info-box rad-txt-warning">
                        <i class="fa fa-clock"></i>
                        <span class="heading">Expiring Soon</span>
                        <span class="value"><span>{{ $expiringContracts }}</span></span>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-6">
                    <div class="rad-info-box rad-txt-danger">
                        <i class="fa fa-times-circle"></i>
                        <span class="heading">Expired Contracts</span>
                        <span class="value"><span>{{ $expiredContracts }}</span></span>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-6">
                    <div class="rad-info-box rad-txt-primary">
                        <i class="fa fa-building"></i>
                        <span class="heading">Departments</span>
                        <span class="value"><span>{{ $totalDepartments }}</span></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                                <span class="value"><span>{{ $expiringContracts }}</span></span>
                            </div>
                        </div>
                        <div class="col-lg-3 col-xs-6">
                            <div class="rad-info-box rad-txt-danger">
                                <i class="fa fa-times-circle"></i>
                                <span class="heading">Expired Contracts</span>
                                <span class="value"><span>{{ $expiredContracts }}</span></span>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">User Distribution
                                        <ul class="rad-panel-action">
                                            <li><i class="fa fa-refresh"></i></li>
                                            <li><i class="fa fa-close"></i></li>
                                        </ul>
                                    </h3>
                                </div>
                                <div class="panel-body">
                                    <canvas id="userChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Contract Status
                                        <ul class="rad-panel-action">
                                            <li><i class="fa fa-refresh"></i></li>
                                            <li><i class="fa fa-close"></i></li>
                                        </ul>
                                    </h3>
                                </div>
                                <div class="panel-body">
                                    <canvas id="contractChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Recent Activities
                                        <ul class="rad-panel-action">
                                            <li><i class="fa fa-refresh"></i></li>
                                            <li><i class="fa fa-close"></i></li>
                                        </ul>
                                    </h3>
                                </div>
                                <div class="panel-body">
                                    <div class="rad-activity-body">
                                        <div class="rad-list-group group">
                                            @foreach($recentActivities as $activity)
                                            <div class="rad-list-group-item">
                                                <div class="rad-list-icon icon-shadow rad-bg-primary pull-left">
                                                    <i class="fa fa-user"></i>
                                                </div>
                                                <div class="rad-list-content">
                                                    <strong>{{ $activity->user->name ?? 'System' }}</strong>
                                                    <div class="md-text">{{ $activity->description }}</div>
                                                    <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Custom styles for super admin dashboard */
    .rad-info-box {
        margin-bottom: 16px;
        box-shadow: 1px 1px 2px 0 #CCCCCC;
        padding: 20px;
        box-shadow: 0 2px 5px 0 rgba(0, 0, 0, .26);
        background: white !important;
        border-radius: 8px;
    }

    .rad-info-box i {
        display: block;
        background-clip: padding-box;
        margin-right: 15px;
        height: 60px;
        width: 60px;
        border-radius: 100%;
        line-height: 60px;
        text-align: center;
        font-size: 4.4em;
        position: absolute;
    }

    .rad-info-box .value,
    .rad-info-box .heading {
        display: block;
        position: relative;
        color: #515d6e;
        text-align: right;
        z-index: 10;
    }

    .rad-info-box .heading {
        font-size: 1.2em;
        font-weight: 300;
        text-transform: uppercase;
    }

    .rad-info-box .value {
        font-size: 2.1em;
        font-weight: 600;
        margin-top: 5px;
    }

    .rad-txt-success i { color: #23AE89; }
    .rad-txt-primary i { color: #1C7EBB; }
    .rad-txt-danger i { color: #E94B3B; }
    .rad-txt-warning i { color: #F98E33; }

    .panel {
        box-shadow: 0 2px 5px 0 rgba(0, 0, 0, .26);
        border-radius: 0 !important;
        position: relative;
        transition: all .2s linear;
    }

    .panel-heading {
        background: white !important;
        border-radius: 0;
        padding: 15px;
        border-bottom: 0 !important;
    }

    .panel-title {
        text-transform: uppercase;
        font-weight: 600;
        font-size: 13px;
        color: #515d6e;
    }

    .rad-panel-action {
        list-style: none;
        display: inline-block;
        float: right;
    }

    .rad-panel-action li {
        display: inline-block;
        color: #515d6e;
        margin: 0 5px;
        cursor: pointer;
    }

    .rad-activity-body {
        position: relative;
    }

    .rad-list-group-item {
        margin: 5px 10px 25px 5px;
    }

    .rad-list-group-item:after {
        content: "";
        display: table;
        clear: both;
    }

    .rad-list-icon {
        position: relative;
        height: 40px;
        width: 40px;
        border-radius: 100%;
        line-height: 40px;
        text-align: center;
        color: white;
        top: -5px;
    }

    .icon-shadow {
        box-shadow: 0 0px 3px 1px rgba(0, 0, 0, .26);
    }

    .rad-bg-primary {
        background: #1C7EBB;
    }

    .rad-list-content {
        line-height: 1.3;
        margin-left: 50px;
    }

    .rad-list-group.group:before {
        content: "";
        position: absolute;
        background: rgba(198, 198, 198, .3);
        top: -20px;
        bottom: -20px;
        left: 25px;
        width: 1px;
    }
</style>
@endsection

@section('scripts')
<script>
    // User Distribution Chart
    const userCtx = document.getElementById('userChart').getContext('2d');
    new Chart(userCtx, {
        type: 'doughnut',
        data: {
            labels: ['Active Users', 'Inactive Users', 'Admin Users'],
            datasets: [{
                data: [{{ $activeUsers }}, {{ $inactiveUsers }}, {{ $adminUsers }}],
                backgroundColor: ['#23AE89', '#E94B3B', '#1C7EBB'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });

    // Contract Status Chart
    const contractCtx = document.getElementById('contractChart').getContext('2d');
    new Chart(contractCtx, {
        type: 'bar',
        data: {
            labels: ['Active', 'Expiring Soon', 'Expired'],
            datasets: [{
                label: 'Contracts',
                data: [{{ $activeContracts }}, {{ $expiringContracts }}, {{ $expiredContracts }}],
                backgroundColor: ['#23AE89', '#F98E33', '#E94B3B'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    function refreshData() {
        location.reload();
    }
</script>
@endsection
