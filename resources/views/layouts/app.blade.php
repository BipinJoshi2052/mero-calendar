<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kharcha App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/style.css?v=3') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
	<link rel="shortcut icon" type="image/png" href="{{ asset('images/favicon.ico') }}">
    <script>
        var baseUrl = '/';
        if(window.location.hostname != 'localhost'){
            baseUrl = '/mero-calendar/public/';
        }
    </script>
</head>

<body>    
    <div class="container">
        <div class="row">
            <div class="header">
                <a href="{{route('home')}}"><h1>Kharcha App</h1></a> 

                <!-- Hamburger Button -->
                <button class="hamburger" id="hamburger-btn">
                    &#9776; <!-- Unicode for the hamburger icon (three bars) -->
                </button>

            </div>
            @yield('content')
        </div>
    </div>

    <!-- Modal -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add Entry</h2>
                <span class="close">&times;</span>
            </div>
            <form id="entryForm">
                @csrf
                <div class="form-group">
                    <label for="entryType">Type:</label>
                    <select name="type" id="entryType" required>
                        <option value="">Select Type</option>
                        <option value="1">Income</option>
                        <option value="2">Expense</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="entryTitle">Title:</label>
                    <input name="title" type="text" id="entryTitle" required>
                </div>
                <div class="form-group">
                    <label for="entryCategory">Category:</label>
                    <select name="category_id" id="entryCategory" required>
                        <option value="">Select Category</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="entrySubCategory">Sub Category:</label>
                    <select name="sub_category_id" id="entrySubCategory" required>
                        <option value="">Select Sub Category</option>
                    </select>
                </div>
                <div class="form-group" id="amountGroup" style="display: none;">
                    <label for="entryAmount">Amount:</label>
                    <input name="amount" type="number" id="entryAmount" step="0.01">
                </div>
                <button type="submit" class="btn">Save Entry</button>
            </form>
        </div>
    </div>

            <!-- Side Navigation -->
            <div class="sidenav" id="sidenav">
                <a href="{{route('home')}}">Home</a>
                @auth
                    <a href="{{route('transactions.index')}}">Transactions</a>
                       <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                        </a>
                    {{-- <a href="{{route('logout')}}">Logout</a> --}}
                @endauth
            </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <?php 
            $isAuthenticated = Auth::check() ? 1 : 0;
        ?>
        @if (request()->is('/'))
            <script>
                // Global variables
                let currentDate = new Date();
                let selectedDate = new Date();
                let calendarData = {};
                let lastClickedDate = currentDate;
                const eventsData = @json(isset($data['events']) ? $data['events'] : []);
                const transactions = @json(isset($data['transactions']) ? $data['transactions'] : []);
                const isAuthenticated = '<?php echo $isAuthenticated; ?>';
                var baseUrl = $('meta[name="base-url"]').attr('content');

                // Dummy data
                function initializeDummyData() {
                    const today = new Date();
                    // console.log(eventsData)
                    // Iterate over events to populate the calendar
                    eventsData.forEach(event => {
                        const eventDate = new Date(event.created_at).toISOString().split('T')[0];
                        const dateKey = new Date(eventDate).toDateString(); // Get YYYY-MM-DD format

                        if (!calendarData[dateKey]) {
                            calendarData[dateKey] = [];
                        }

                        // Add event data to the calendar
                        calendarData[dateKey].push({
                            type: 'event',
                            title: event.title,
                            date: dateKey
                        });
                    });
                    // console.log(calendarData)

                    // Iterate over transactions to populate the calendar
                    transactions.forEach(transaction => {
                        const transactionDate = new Date(transaction.transaction_date);
                        const dateKey = transactionDate.toDateString(); // Use the date as a key

                        if (!calendarData[dateKey]) {
                            calendarData[dateKey] = [];
                        }

                        // Add transaction data to the calendar
                        calendarData[dateKey].push({
                            type: transaction.type === 1 ? 'income' : 'expense', // Type 1 for income, Type 2 for expense
                            title: transaction.title,
                            category: transaction.category.title, // Map to category name if you have category names
                            subCategory: transaction.sub_category.title, // Map to subcategory name if you have subcategory names
                            amount: transaction.amount,
                            date: dateKey
                        });
                    });
                    // console.log(calendarData)
                }
                function initializeDummyData2() {
                    return;
                }

                // Category data
                const categories = @json(isset($data['categories']) ? $data['categories'] : []);

                // Initialize the application
                $(document).ready(function() {
                    initializeDummyData();
                    initializeCalendar();
                    populateYearSelect();
                    setCurrentDate();
                    generateCalendar();
                    updateDateDisplay();
                    bindEvents();
                });

                function initializeCalendar() {
                    $('#monthSelect').val(currentDate.getMonth());
                    $('#yearSelect').val(currentDate.getFullYear());
                }

                function populateYearSelect() {
                    const currentYear = new Date().getFullYear();
                    const yearSelect = $('#yearSelect');
                    
                    for (let year = currentYear - 10; year <= currentYear + 10; year++) {
                        yearSelect.append(`<option value="${year}">${year}</option>`);
                    }
                    
                    yearSelect.val(currentYear);
                }

                function setCurrentDate() {
                    const month = parseInt($('#monthSelect').val());
                    const year = parseInt($('#yearSelect').val());
                    currentDate = new Date(year, month, 1);
                    selectedDate = new Date();
                }

                function generateCalendar() {
                    const month = parseInt($('#monthSelect').val());
                    const year = parseInt($('#yearSelect').val());
                    const today = new Date();
                    
                    const firstDay = new Date(year, month, 1);
                    const lastDay = new Date(year, month + 1, 0);
                    const startDate = new Date(firstDay);
                    startDate.setDate(startDate.getDate() - firstDay.getDay());
                    
                    const calendarBody = $('#calendarBody');
                    calendarBody.empty();
                    
                    let currentWeek = startDate;
                    
                    for (let week = 0; week < 6; week++) {
                        const row = $('<tr></tr>');
                        
                        for (let day = 0; day < 7; day++) {
                            const cellDate = new Date(currentWeek);
                            const cell = $('<td></td>');
                            cell.text(cellDate.getDate());
                            cell.data('date', cellDate.toDateString());
                            
                            if (cellDate.getMonth() !== month) {
                                cell.addClass('other-month');
                            }
                            
                            if (cellDate.toDateString() === today.toDateString()) {
                                cell.addClass('today');
                            }
                            
                            if (cellDate.toDateString() === selectedDate.toDateString()) {
                                cell.addClass('selected');
                            }
                            
                            if (hasDataForDate(cellDate)) {
                                cell.addClass('has-data');
                            }
                            
                            row.append(cell);
                            currentWeek.setDate(currentWeek.getDate() + 1);
                        }
                        
                        calendarBody.append(row);
                    }
                }

                function hasDataForDate(date) {
                    const dateKey = date.toDateString();
                    return calendarData[dateKey] && calendarData[dateKey].length > 0;
                }

                function updateDateDisplay() {
                    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                    $('#currentDateDisplay').text(selectedDate.toLocaleDateString('en-US', options));
                    updateTabContent();
                }

                function updateTabContent() {
                    const dateKey = selectedDate.toDateString();
                    const data = calendarData[dateKey] || [];
                    // console.log(data)
                    
                    // Update Events tab
                    const eventsList = $('#eventsList');
                    eventsList.empty();
                    
                    const events = data.filter(item => item.type === 'event');
                    // console.log(events)
                    if (events.length === 0) {
                        eventsList.append('<p>No events for this date.</p>');
                    } else {
                        events.forEach(event => {
                            eventsList.append(`
                                <div class="event-item">
                                    <strong>${event.title}</strong><br>
                                </div>
                            `);
                        });
                    }
                    
                    // Update Info tab
                    const infoList = $('#infoList');
                    infoList.empty();
                    // console.log(data);
                    
                    const financialData = data.filter(item => item.type === 'income' || item.type === 'expense');
                    if (financialData.length === 0) {
                        infoList.append('<p>No financial data for this date.</p>');
                    } else {
                        let totalIncome = 0;
                        let totalExpense = 0;
                        
                        financialData.forEach(item => {
                            const amount = parseFloat(item.amount) || 0;
                            if (item.type === 'income') {
                                totalIncome += amount;
                            } else {
                                totalExpense += amount;
                            }
                            
                            infoList.append(`
                                <div class="info-item ${item.type}">
                                    <strong>${item.title}</strong> - <span class="amount">${amount.toFixed(2)}</span><br>
                                    <small>Category: ${item.category} - ${item.subCategory}</small>
                                </div>
                            `);
                        });
                        
                        infoList.prepend(`
                            <div style="background: linear-gradient(135deg, #e8f5e8, #d4f4dd); padding: 15px; border-radius: 10px; margin-bottom: 15px; border: 1px solid #27ae60;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                    <strong style="color: #27ae60;">Total Income:</strong>
                                    <strong style="color: #27ae60;">${totalIncome.toFixed(2)}</strong>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                    <strong style="color: #e74c3c;">Total Expense:</strong>
                                    <strong style="color: #e74c3c;">${totalExpense.toFixed(2)}</strong>
                                </div>
                                <hr style="border: none; border-top: 1px solid #27ae60; margin: 10px 0;">
                                <div style="display: flex; justify-content: space-between;">
                                    <strong style="color: #333;">Net Amount:</strong>
                                    <strong style="color: ${totalIncome - totalExpense >= 0 ? '#27ae60' : '#e74c3c'};">${(totalIncome - totalExpense).toFixed(2)}</strong>
                                </div>
                            </div>
                        `);
                    }
                }

                function bindEvents() {
                    // Date control changes
                    $('#monthSelect, #yearSelect').on('change', function() {
                        generateCalendar();
                    });
                    
                    // Navigation arrows
                    $('#prevMonth').on('click', function() {
                        navigateMonth(-1);
                    });
                    
                    $('#nextMonth').on('click', function() {
                        navigateMonth(1);
                    });
                    
                    // Calendar cell clicks - double click logic
                    $(document).on('click', '.calendar td:not(.other-month)', function() {
                        const dateStr = $(this).data('date');
                        const clickedDate = new Date(dateStr);
                        
                        // Remove previous selection
                        $('.calendar td').removeClass('selected');
                        $(this).addClass('selected');
                        
                        // Check if this is the same date clicked again
                        if (lastClickedDate && lastClickedDate.toDateString() === clickedDate.toDateString()) {
                            // Second click - open modal

                            if (isAuthenticated === 0) {
                                return;
                            }
                            selectedDate = clickedDate;
                            $('#modal').addClass('show').show();
                        } else {
                            // First click - just select and show data
                            selectedDate = clickedDate;
                            lastClickedDate = clickedDate;
                            updateDateDisplay();
                        }
                    });
                    
                    // Tab switching
                    $('.tab').on('click', function() {
                        const tabName = $(this).data('tab');
                        $('.tab').removeClass('active');
                        $(this).addClass('active');
                        $('.tab-pane').removeClass('active');
                        $(`#${tabName}`).addClass('active');
                    });
                    
                    // Modal controls
                    $('.close').on('click', function() {
                        $('#modal').removeClass('show');
                        setTimeout(() => $('#modal').hide(), 300);
                    });
                    
                    $(window).on('click', function(event) {
                        if (event.target.id === 'modal') {
                            $('#modal').removeClass('show');
                            setTimeout(() => $('#modal').hide(), 300);
                        }
                    });
                    
                    // Entry type change
                    $('#entryType').on('change', function() {
                        const type = ($(this).val() == 1) ? 'income' : 'expense';
                        populateCategories(type);
                        
                        if (type === 'income' || type === 'expense') {
                            $('#amountGroup').show();
                            $('#entryAmount').prop('required', true);
                        } else {
                            $('#amountGroup').hide();
                            $('#entryAmount').prop('required', false);
                        }
                    });
                    
                    // Category change
                    $('#entryCategory').on('change', function() {
                        const type = $('#entryType').val();
                        const category = $(this).val();
                        populateSubCategories(type, category);
                    });
                    
                    // Form submission
                    $('#entryForm').on('submit', function(e) {
                        e.preventDefault();
                        saveEntry();
                        $('#modal').removeClass('show');
                        setTimeout(() => $('#modal').hide(), 300);
                    });
                }

                function populateCategories(type) {
                    const categorySelect = $('#entryCategory');
                    categorySelect.empty().append('<option value="">Select Category</option>');
                    if (type && categories[type]) {
                        categories[type].forEach((category, index) => {
                            // Add the index as a data attribute
                            categorySelect.append(`<option value="${category.id}" data-index="${index}">${category.title}</option>`);
                        });
                    }
                    
                    $('#entrySubCategory').empty().append('<option value="">Select Sub Category</option>');
                }

                function populateSubCategories(type, categoryId) {
                    const subCategorySelect = $('#entrySubCategory');
                    subCategorySelect.empty().append('<option value="">Select Sub Category</option>');
                    type = (type == 1) ? 'income' : 'expense';
                    
                    // Check if the type exists and if the categoryId exists within that type
                    if (type !== undefined && categories[type]) {
                        // Find the selected option based on categoryId and retrieve the index from the data attribute
                        const selectedOption = $('#entryCategory option[value="' + categoryId + '"]');
                        const categoryIndex = selectedOption.data('index');

                        if (categoryIndex !== undefined) {
                            const category = categories[type][categoryIndex];

                            if (category && category.sub_categories) {
                                // Loop through the subcategories and append them to the select dropdown
                                category.sub_categories.forEach(subCategory => {
                                    subCategorySelect.append(`<option value="${subCategory.id}">${subCategory.title}</option>`);
                                });
                            } else {
                                console.log('No subcategories found for this category');
                            }
                        } else {
                            console.log('Category index not found');
                        }
                    } else {
                        console.log('Invalid type or category data');
                    }
                }

                function saveEntry() {
                    const dateKey = selectedDate.toDateString();
                    const entry = {
                        type: $('#entryType').val(),
                        title: $('#entryTitle').val(),
                        category: $('#entryCategory').val(),
                        subCategory: $('#entrySubCategory').val(),
                        amount: $('#entryAmount').val(),
                        date: selectedDate.toDateString()
                    };

                    // Perform the AJAX request to store the transaction
                    $.ajax({
                        url: baseUrl + '/transactions', // Dynamically use base URL
                        method: 'POST',
                        data: {
                            title: entry.title,
                            type: entry.type,
                            category_id: entry.category,
                            sub_category_id: entry.subCategory,
                            amount: entry.amount,
                            transaction_date: entry.date, // Send the raw date string to be processed in the controller
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Set the CSRF token header
                        },
                        success: function(response) {
                            // console.log('Transaction saved:', response);
                            if (!calendarData[dateKey]) {
                                calendarData[dateKey] = [];
                            }
                            entry.category = $('#entryCategory option:selected').text();
                            entry.subCategory = $('#entrySubCategory option:selected').text();
                            entry.type = ($('#entryType').val() == 1) ? 'income' : 'expense';

                            calendarData[dateKey].push(entry);
                            
                            // Reset form
                            $('#entryForm')[0].reset();
                            $('#amountGroup').hide();
                            $('#entryAmount').prop('required', false);
                            
                            // Update display
                            updateTabContent();
                            generateCalendar();
                        },
                        error: function(xhr, status, error) {
                            console.log('Error:', error);
                        }
                    });
                }

                function navigateMonth(direction) {
                    let currentMonth = parseInt($('#monthSelect').val());
                    let currentYear = parseInt($('#yearSelect').val());
                    
                    currentMonth += direction;
                    
                    if (currentMonth > 11) {
                        currentMonth = 0;
                        currentYear++;
                    } else if (currentMonth < 0) {
                        currentMonth = 11;
                        currentYear--;
                    }
                    
                    $('#monthSelect').val(currentMonth);
                    $('#yearSelect').val(currentYear);
                    
                    generateCalendar();
                }
            </script>
        @endif
        <script>
            // Get the hamburger button, sidenav, and the document elements
            const hamburgerBtn = document.getElementById("hamburger-btn");
            const sidenav = document.getElementById("sidenav");
            const body = document.body;

            // Toggle the sidenav on hamburger button click
            hamburgerBtn.addEventListener("click", function(event) {
                event.stopPropagation(); // Prevent the click from propagating to the body
                sidenav.classList.toggle("open");

                // Toggle the active class to add animation on button
                hamburgerBtn.classList.toggle("active");
            });

            // Close the sidenav if clicking outside of it
            body.addEventListener("click", function(event) {
                if (sidenav.classList.contains("open") && !sidenav.contains(event.target) && !hamburgerBtn.contains(event.target)) {
                    sidenav.classList.remove("open");
                    hamburgerBtn.classList.remove("active");
                }
            });
        </script>
        @yield('scripts')
</html>

