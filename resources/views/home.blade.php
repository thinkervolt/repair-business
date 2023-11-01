@extends('layouts.admin')
@section('page')
    Dashboard
@endsection

@section('page-content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        @if (session()->has('error'))
            <div class="alert {{ session()->get('alert') }} alert-dismissible fade show">
                <li>{{ session()->get('error') }}</li>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        </div>

        <!-- Content Row -->
        <div class="row">
            <!-- Earnings (Daily)  -->
            <div class="col-lg mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Earnings - Today
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    $ {{ number_format((float) $current_year_income['current_day'], 2, '.', ',') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Earnings (Monthly)  -->
            <div class="col-lg mb-4">
                <div class="card border-left-success  shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success  text-uppercase mb-1">Earnings -
                                    {{ date('M') }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    $ {{ number_format((float) $current_year_income['current_month'], 2, '.', ',') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Earnings (Yearly) Card Example -->
            <div class="col-lg mb-4">
                <div class="card border-left-success  shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success  text-uppercase mb-1">Earnings -
                                    {{ date('Y') }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    $ {{ number_format((float) $current_year_income['total'], 2, '.', ',') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-lg mb-4">
                <div class="card border-left-secondary shadow h-100 py-2">
                    <div class="card-body">
                        <a style="text-decoration:none;" href="{{ route('index-repair', ['no-invoice', 0]) }}">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Repairs not Invoiced
                                    </div>
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-auto">
                                            <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                                {{ $repairs_no_invoice }}</div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-tools fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pending Requests Card Example -->
            <div class="col-lg mb-4">
                <div class="card border-left-secondary shadow h-100 py-2">
                    <div class="card-body">
                        <a style="text-decoration:none;" href="{{ route('index-invoice', 'unpaid') }}">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Unpaid Invoices
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $unpaid_invoices }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Row -->

        <div class="row">

            <!-- Area Chart -->
            <div class="col">
                <div class="card shadow mb-4 border-left-primary">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Earnings Overview</h6>

                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="myAreaChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
@endsection

@section('scripts')
    <!-- Page level plugins -->
    <script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
    <script>
        // Set new default font family and font color to mimic Bootstrap's default styling
        Chart.defaults.global.defaultFontFamily = 'Nunito',
            '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.global.defaultFontColor = '#858796';

        function number_format(number, decimals, dec_point, thousands_sep) {
            // *     example: number_format(1234.56, 2, ',', ' ');
            // *     return: '1 234,56'
            number = (number + '').replace(',', '').replace(' ', '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function(n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + Math.round(n * k) / k;
                };
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        }

        // Area Chart Example
        var ctx = document.getElementById("myAreaChart");
        var myLineChart = new Chart(ctx, {
            type: 'line',

            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                datasets: [{
                        label: "Earnings {{ date('Y') }}",
                        lineTension: 0.3,
                        backgroundColor: "rgba(78, 115, 223, 0.05)",
                        borderColor: "rgba(78, 115, 223, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointBorderColor: "rgba(78, 115, 223, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,


                        data: [
                            {{ $current_year_income['jan'] }},
                            {{ $current_year_income['feb'] }},
                            {{ $current_year_income['mar'] }},
                            {{ $current_year_income['apr'] }},
                            {{ $current_year_income['may'] }},
                            {{ $current_year_income['jun'] }},
                            {{ $current_year_income['jul'] }},
                            {{ $current_year_income['aug'] }},
                            {{ $current_year_income['sep'] }},
                            {{ $current_year_income['oct'] }},
                            {{ $current_year_income['nov'] }},
                            {{ $current_year_income['dec'] }}
                        ],
                    },
                    {
                        label: "Earnings {{ date('Y', strtotime('last year')) }}",
                        lineTension: 0.3,
                        backgroundColor: "rgba(78, 115, 223, 0.05)",
                        borderColor: "rgba(197, 197, 197, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(125, 125, 125, 1)",
                        pointBorderColor: "rgba(125, 125, 125, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(125, 125, 125, 1)",
                        pointHoverBorderColor: "rgba(125, 125, 125, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: [
                            {{ $past_year_income['jan'] }},
                            {{ $past_year_income['feb'] }},
                            {{ $past_year_income['mar'] }},
                            {{ $past_year_income['apr'] }},
                            {{ $past_year_income['may'] }},
                            {{ $past_year_income['jun'] }},
                            {{ $past_year_income['jul'] }},
                            {{ $past_year_income['aug'] }},
                            {{ $past_year_income['sep'] }},
                            {{ $past_year_income['oct'] }},
                            {{ $past_year_income['nov'] }},
                            {{ $past_year_income['dec'] }}
                        ],
                    }
                ],

            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    xAxes: [{
                        time: {
                            unit: 'date'
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            // Include a dollar sign in the ticks
                            callback: function(value, index, values) {
                                return '$' + number_format(value);
                            }
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }],
                },
                legend: {
                    display: false
                },
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 14,
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    intersect: false,
                    mode: 'index',
                    caretPadding: 10,
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return datasetLabel + ': $' + number_format(tooltipItem.yLabel);
                        }
                    }
                }
            }
        });
    </script>
@endsection
