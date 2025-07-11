@extends('layouts.app')

@section('content')
    @section('styles')
        <!-- DataTables CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <style>
            /* Make the table scrollable horizontally */
            .dataTables-wrapper {
                overflow-x: auto;
            }

            /* Fix DataTable buttons and search bar */
            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate {
                position: sticky;
                top: 0;
                z-index: 100; /* Ensure it stays above the table */
                padding: 10px;
            }
        </style>
    @endsection

<?php
    use Carbon\Carbon;
    // echo request('from');
?>
<div class="container">
    <div class="transactions-div">
        <div id="info-message">
            <!-- Information about income and expense will be populated here -->
            <p id="income-expense-info">
                Total Income - Rs. <span id="total-income">--</span> in <span id="total-income-count">--</span> transactions
                &nbsp;&nbsp;&nbsp;&nbsp;
                Total Expense - Rs. <span id="total-expense">--</span> in <span id="total-expense-count">--</span>
                transactions
                {{-- You have earned a total income of Rs. <span id="total-income">--</span> from <span id="total-income-count">--</span> 
                and made a total expense of Rs. <span id="total-expense">--</span> from <span id="total-expense-count">--</span> transaction(s) in this period. --}}
            </p>
        </div>
        <!-- Date Range Filter Form -->
        <form method="GET" action="{{ route('transactions.index') }}" class="mb-4 row filter-div">
            {{-- <div class="row d-flex filter-div"> --}}
                <!-- Category Filter -->
                <div class="col-md-2">
                    <label>Category</label>
                    <select name="category_id" id="category_id" class="form-control">
                        <option value="">Select Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request()->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- Subcategory Filter -->
                <div class="col-md-2">
                    <label>Subcategory</label>
                    <select name="sub_category_id" id="sub_category_id" class="form-control">
                        <option value="">Select Subcategory</option>
                        @foreach ($subcategories as $subcategory)
                            <option value="{{ $subcategory->id }}"
                                {{ request()->sub_category_id == $subcategory->id ? 'selected' : '' }}>
                                {{ $subcategory->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label>Date Range</label>
                    <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span>{{ request('start_date', Carbon::now()->subDays(15)->format('Y-m-d')) }} - {{ request('end_date', Carbon::now()->format('Y-m-d')) }}</span> <i class="fa fa-caret-down"></i>
                    </div>
                    <input type="hidden" name="start_date" id="start-date" value="{{ request('start_date', Carbon::now()->subDays(15)->format('Y-m-d')) }}">
                    <input type="hidden" name="end_date" id="end-date" value="{{ request('end_date', Carbon::now()->format('Y-m-d')) }}">
                </div>
                {{-- <div class="col-md-2">
                    <label>From Date</label>
                    <input type="date" placeholder="Select Date" id="start_date" name="start_date" class="form-control"
                        value="{{ request()->start_date }}">
                </div>
                <div class="col-md-2">
                    <label>To Date</label>
                    <input type="date" placeholder="Select Date" id="end_date" name="end_date" class="form-control"
                        value="{{ request()->end_date }}">
                </div> --}}
                <div class="col-md-3 filter-btn-div align-items-end">
                    <button type="submit" class="btn btn-primary filter-btn">
                        <i class="fa fa-filter"></i>
                        Filter
                    </button>
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary ml-2 filter-btn">
                        <i class="fa fa-redo"></i>
                        Reset
                    </a>
                </div>
            {{-- </div> --}}
        </form>

        <div class="dataTables-wrapper">
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
                    {{-- @foreach ($transactions as $transaction)
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
        </div>
        

        <!-- Pagination Links -->
        {{-- <div class="mt-3">
                {{ $transactions->links() }}
            </div> --}}

    </div>
    </div>
@endsection

@section('scripts')
    <!-- Include DataTables CDN -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.2.0/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script> --}}
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>


    <script>
        $(document).ready(function() {
            $(function() {
                // Get the 'from' and 'to' date values from the query parameters
                var urlParams = new URLSearchParams(window.location.search);
                var start = urlParams.get('start_date') ? moment(urlParams.get('start_date')) : moment().subtract(15, 'days');
                var end = urlParams.get('end_date') ? moment(urlParams.get('end_date')) : moment();

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
            // document.addEventListener('DOMContentLoaded', function () {
            // console.log('hjere')
            // const startDateInput = document.getElementById('start_date');
            // const endDateInput = document.getElementById('end_date');

            // Set default date format as placeholder in case it's supported
            // if (!startDateInput.value) startDateInput.setAttribute('placeholder', 'DD/MM/YYYY');
            // if (!endDateInput.value) endDateInput.setAttribute('placeholder', 'DD/MM/YYYY');
            // }); 
            // Initialize the DataTable
            $('#transactions-table1').DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                ajax: {
                    url: '/transactions', // Or the appropriate route
                    type: 'GET',
                    data: function(d) {
                        // Add selected filters to the DataTable request
                        d.start_date = $('input[name="start_date"]').val();
                        d.end_date = $('input[name="end_date"]').val();
                        d.category_id = $('#category_id').val();
                        d.sub_category_id = $('#sub_category_id').val();
                    },
                    success: function(data) {
                        // Update the information message with income and expense data
                        $('#total-income').text(data.totalIncome);
                        $('#total-income-count').text(data.totalIncomeCount);
                        $('#total-expense').text(data.totalExpense);
                        $('#total-expense-count').text(data.totalExpenseCount);
                    }
                },
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        },
                        orderable: false
                    },
                    {
                        data: 'title'
                    },
                    {
                        data: 'category.title'
                    },
                    {
                        data: 'sub_category.title'
                    },
                    {
                        data: 'amount'
                    },
                    {
                        data: 'transaction_date',
                        render: function(data) {
                            return moment(data).format('ddd, DD MMMM, YYYY');
                        }
                    }
                ],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                dom: 'Bfrtip',
                buttons: [
                    'excelHtml5',
                    'pdfHtml5',
                ],
            });

            // Initialize DataTable
            $('#transactions-table').DataTable({
                processing: true,
                responsive: true,
                serverSide: true,
                ordering: false,
                ajax: {
                    url: baseUrl + 'transactions',
                    // url: (window.location.hostname === 'localhost') ? '/transactions' : '/mero-calendar/public/transactions', // Switch URL based on environment
                    type: 'GET',
                    data: function(d) {
                        // Add selected filters to the DataTable request
                        d.start_date = $('input[name="start_date"]').val();
                        d.end_date = $('input[name="end_date"]').val();
                        d.category_id = $('#category_id').val();
                        d.sub_category_id = $('#sub_category_id').val();
                        d.search_value = d.search.value; // Include search value for filtering
                    },
                    complete: function(xhr, status) {
                        var data = xhr.responseJSON; // Access the response JSON

                        // Update the information message with income and expense data
                        $('#total-income').text(data.totalIncome);
                        $('#total-income-count').text(data.totalIncomeCount);
                        $('#total-expense').text(data.totalExpense);
                        $('#total-expense-count').text(data.totalExpenseCount);
                    }
                },
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        },
                        orderable: false
                    },
                    {
                        data: 'title',
                        render: function(data, type, row) {
                            // console.log(row)
                            // Check the type of the transaction and append an icon
                            var icon = '';
                            var color = '';
                            if (row.type === 1) {
                                icon =
                                '<i class="fa fa-arrow-up" style="color: green;"></i>'; // Green up arrow for income
                                color = 'green';
                            } else if (row.type === 0) {
                                icon =
                                '<i class="fa fa-arrow-down" style="color: red;"></i>'; // Red down arrow for expense
                                color = 'red';
                            }
                            // return data + ' ' + icon; // Append the icon next to the title
                            return icon + ' ' + data; // Append the icon next to the title
                        }
                    },
                    {
                        data: 'category.title'
                    },
                    {
                        data: 'sub_category.title'
                    },
                    {
                        data: 'amount'
                    },
                    {
                        data: 'transaction_date',
                        render: function(data) {
                            return moment(data).format('ddd, DD MMMM, YYYY');
                        }
                    }
                ],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                dom: 'Bfrtip',
                buttons: [
                    'excelHtml5',
                    'pdfHtml5',
                ],
                language: {
                    info: "Showing _START_ to _END_ of _TOTAL_ entries", // Customize the info text
                    infoEmpty: "Showing 0 to 0 of 0 entries", // When no data is available
                    infoFiltered: "", // Remove the filtered message
                    lengthMenu: "Show _MENU_ entries", // Page length options label
                }
            });
            // Open calendar when user clicks anywhere in the input field (start_date and end_date)
            $('input[name="start_date"], input[name="end_date"]').on('click', function() {
                this.showPicker(); // Always trigger the date picker on click
            });

            // Open the calendar for the end date when the start date is selected
            $('input[name="start_date"]').on('change', function() {
                const endDateInput = $('input[name="end_date"]');
                endDateInput.focus(); // Focus on the end date field
            });

            // Ensure the calendar opens when focus is set to the input field
            $('input[name="end_date"], input[name="start_date"]').on('focus', function() {
                setTimeout(() => {
                    this
                .showPicker(); // Trigger the date picker after focus, regardless of the value
                }, 0);
            });

            if (!$('#category_id').val()) {
                $('#sub_category_id').prop('disabled', true);
            }
            // Dynamically populate subcategories based on selected category
            $('#category_id').change(function() {
                var categoryId = $(this).val();

                if (!categoryId) {
                    // If no category is selected, disable the subcategory dropdown
                    $('#sub_category_id').prop('disabled', true);
                } else {
                    // If a category is selected, enable the subcategory dropdown
                    $('#sub_category_id').prop('disabled', false);

                    // Make an AJAX request to get subcategories related to the selected category
                    $.ajax({
                        url: (window.location.hostname === 'localhost') ? ('/get-subcategories/' +
                            categoryId) : ('/mero-calendar/public/get-subcategories/' +
                            categoryId),
                        // url: '/get-subcategories/' + categoryId,
                        type: 'GET',
                        success: function(data) {
                            console.log(data)
                            var subcategorySelect = $('#sub_category_id');
                            subcategorySelect.empty(); // Clear existing options
                            subcategorySelect.append(
                                '<option value="">Select Subcategory</option>'
                                ); // Default option

                            // Add the fetched subcategories
                            data.forEach(function(subcategory) {
                                subcategorySelect.append('<option value="' + subcategory
                                    .id + '">' + subcategory.title + '</option>');
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection
