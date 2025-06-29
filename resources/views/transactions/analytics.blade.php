@extends('layouts.app')

@section('content')
    @section('styles')

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <style> 
            #doughnutChart{
                max-width: 500px;
                max-height: 500px;
            }
            .doughnutChart{
                text-align: center;
            }
            .chart-container {
                position: relative;
                width: 100%;
                height: 400px; /* Adjust height as per requirement */
                overflow-x: auto; /* Enable horizontal scrolling */
            }
            @media (max-width: 768px) {
                .chart-container {
                    height: 300px; /* You can increase the height on mobile if needed */
                }
                .chart-container canvas {
                    width: 500px !important; /* Make canvas take the full width */
                    height: 275px !important; /* Maintain the aspect ratio */
                }
            }
    </style>
    @endsection
    <?php
        use Carbon\Carbon;
        // echo request('from');
    ?>
<div class="container">

    <form method="GET" action="{{ route('analytics.index') }}" class="mb-4 row filter-div">
        <div class="col-md-3">
            <label>Type</label>
            <select name="type" class="form-control">
                <option value="">Select Type</option>
                <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Income</option>
                <option value="expense" {{ request('type') == 'expense' ? 'selected' : (request('type') == '' ? 'selected' : '') }}>Expense</option>
                <option value="category" {{ request('type') == 'category' ? 'selected' : '' }}>Category</option>
            </select>
        </div>
        <div class="col-md-6">
            <label>Date Range</label>
            <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                <i class="fa fa-calendar"></i>&nbsp;
                <span>{{ request('from', Carbon::now()->subDays(15)->format('Y-m-d')) }} - {{ request('to', Carbon::now()->format('Y-m-d')) }}</span> <i class="fa fa-caret-down"></i>
            </div>
            <input type="hidden" name="from" id="start-date" value="{{ request('from', Carbon::now()->subDays(15)->format('Y-m-d')) }}">
            <input type="hidden" name="to" id="end-date" value="{{ request('to', Carbon::now()->format('Y-m-d')) }}">
        </div>
{{--         
        <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
            <i class="fa fa-calendar"></i>&nbsp;
            <span></span> <i class="fa fa-caret-down"></i>
        </div>

        <div class="col-md-3">
            <label>From Date</label>
            <input type="date" name="from" class="form-control" value="{{ $from }}" onchange="document.querySelector('input[name=to]').focus()" />
        </div>

        <div class="col-md-3">
            <label>To Date</label>
            <input type="date" name="to" class="form-control" value="{{ $to }}" />
        </div> --}}

        <div class="col-md-3 filter-btn-div align-items-end">
            <button type="submit" class="btn btn-primary mr-2">
                <i class="fa fa-filter"></i> Filter
            </button>
            <a href="{{ route('analytics.index', ['type' => request('type')]) }}" class="btn btn-secondary">
                <i class="fa fa-redo"></i> Reset
            </a>
        </div>
    </form>
    @if($chartData)
        <div class="row lineChart">
            <div class="chart-container">
                <canvas id="lineChart"></canvas>
            </div>
        </div>
    @endif

    @if($doughnutData)
        <div class="row doughnutChart">
            <canvas id="doughnutChart" height="100"></canvas>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(function() {
        // Get the 'from' and 'to' date values from the query parameters
        var urlParams = new URLSearchParams(window.location.search);
        var start = urlParams.get('from') ? moment(urlParams.get('from')) : moment().subtract(15, 'days');
        var end = urlParams.get('to') ? moment(urlParams.get('to')) : moment();

        function cb(start, end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $('#start-date').val(start.format('YYYY-MM-DD'));
            $('#end-date').val(end.format('YYYY-MM-DD'));
        }

        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 15 Days': [moment().subtract(15, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end);
    });
    @if($chartData)
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData->keys()) !!},
                datasets: [{
                    label: '{{ ucfirst($type) }}',
                    data: {!! json_encode($chartData->values()) !!},
                    borderWidth: 2,
                    fill: false,
                    tension: 0.4,
                }]
            }
        });
    @endif

    @if($doughnutData)
        const doughnutCtx = document.getElementById('doughnutChart').getContext('2d');
        new Chart(doughnutCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($doughnutData->keys()) !!},
                datasets: [{
                    label: 'Category Expenses',
                    data: {!! json_encode($doughnutData->values()) !!},
                    borderWidth: 1
                }]
            }
        });
    @endif
</script>
@endsection
