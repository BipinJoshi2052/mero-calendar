@extends('layouts.app')

@section('content')
<style> 
    #doughnutChart{
        max-width: 500px;
        max-height: 500px;
    }
    .doughnutChart{
        text-align: center;
    }
</style>
<div class="container">

    <form method="GET" action="{{ route('analytics.index') }}" class="mb-4 row">
        <div class="col-md-3">
            <label>Type</label>
            <select name="type" class="form-control">
                <option value="">Select</option>
                <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Income</option>
                <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expense</option>
                <option value="category" {{ request('type') == 'category' ? 'selected' : '' }}>Category</option>
            </select>
        </div>

        <div class="col-md-3">
            <label>From Date</label>
            <input type="date" name="from" class="form-control" value="{{ $from }}" onchange="document.querySelector('input[name=to]').focus()" />
        </div>

        <div class="col-md-3">
            <label>To Date</label>
            <input type="date" name="to" class="form-control" value="{{ $to }}" />
        </div>

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
        <canvas id="lineChart" height="100"></canvas>
    @endif

    @if($doughnutData)
        <div class="row doughnutChart">
            <canvas id="doughnutChart" height="100"></canvas>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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
