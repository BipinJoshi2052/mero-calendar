@extends('layouts.app')

@section('content')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css">

    <div class="header">
        <a href="{{route('home')}}"><h1>Kharcha App</h1></a> 

        <!-- Hamburger Button -->
        <button class="hamburger" id="hamburger-btn">
            &#9776; <!-- Unicode for the hamburger icon (three bars) -->
        </button>

    </div>
    <div class="transactions-div">
        <!-- Date Range Filter Form -->
        <form method="GET" action="{{ route('transactions.index') }}" class="mb-3">
            <div class="row d-flex filter-div">
                <div class="col-md-3">
                    <input type="date" name="start_date" class="form-control" value="{{ request()->start_date }}">
                </div>
                <div class="col-md-3">
                    <input type="date" name="end_date" class="form-control" value="{{ request()->end_date }}">
                </div>
                <div class="col-md-2 filter-btn-div">
                    <button type="submit" class="btn btn-primary filter-btn">Filter</button>
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary ml-2 filter-btn">Reset</a>
                </div>
            </div>
        </form>

        <!-- Table to display transactions -->
        <table id="transactions-table" class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Sub-Category</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                {{-- @foreach($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->id }}</td>
                        <td>{{ $transaction->category->title }}</td>
                        <td>{{ $transaction->subCategory->title }}</td>
                        <td>{{ $transaction->amount }}</td>
                        <td>{{ $transaction->created_at }}</td>
                    </tr>
                @endforeach --}}
            </tbody>
        </table>

        <!-- Pagination Links -->
        {{-- <div class="mt-3">
            {{ $transactions->links() }}
        </div> --}}

    </div>

@endsection

@section('scripts')
<!-- Include DataTables CDN -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.2.0/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>


<script>
$(document).ready(function() {
    // Initialize the DataTable
    $('#transactions-table').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        ajax: {
            // url: '/transactions', // API route for fetching data
            url: '/mero-calendar/public/transactions', // API route for fetching data
            type: 'GET',
            dataSrc: 'data', // This should match the response data structure
            data: function(d) {
                // Add additional parameters here if needed (e.g., date range)
                d.start_date = $('input[name="start_date"]').val();
                d.end_date = $('input[name="end_date"]').val();
            }
        },
        columns: [
            {
                data: null, // For serial number, don't rely on data directly
                render: function(data, type, row, meta) {
                    return meta.row + 1; // Return the row index + 1 for serial number
                },
                orderable: false // Disable sorting for the serial number column
            },
            { data: 'title' },
            { data: 'category.title' },
            { data: 'sub_category.title' },
            { data: 'amount' },
            {
                data: 'transaction_date',
                render: function(data, type, row) {
                    // Use Moment.js to format the date
                    return moment(data).format('ddd, DD MMMM, YYYY'); // Format as "Mon, 24 April, 2025"
                }
            }
        ],
        pageLength: 10, // Default transactions per page
        lengthMenu: [10, 25, 50, 100], // Options to change transactions per page
        dom: 'Bfrtip', // Buttons placement (Search, Export, Pagination)
        buttons: [
            'excelHtml5', // Export to Excel
            'pdfHtml5', // Export to PDF
        ],
    });
});
</script>

@endsection
