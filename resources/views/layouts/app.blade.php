<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mero Calendar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
</head>

<body>
    <!-- Responsive navbar-->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Mero Calendar</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    {{-- <li class="nav-item"><a class="nav-link active" href="/">Home</a></li> --}}
                    @guest
                        <li class="nav-item"><a class="color-white nav-link" href="{{route('login')}}">Login</a></li>
                        <li class="nav-item"><a class="color-white nav-link" href="{{route('register')}}">Register</a></li>
                    @endguest
                    @auth
                        <!-- Show Logout link if the user is logged in -->
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="color-white nav-link" style="text-decoration: none;">Logout</button>
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
    <!-- Page header with logo and tagline-->
    {{-- <header class="py-5 bg-light border-bottom mb-4">
        <div class="container">
            <div class="text-center my-5">
                <h1 class="fw-bolder">Welcome to Blog Home!</h1>
                <p class="lead mb-0">A Bootstrap 5 starter layout for your next blog homepage</p>
            </div>
        </div>
    </header> --}}
    
    <div class="main-container container">
        <div class="row">
            @yield('content')
        </div>
    </div>
    <!-- Footer-->
    <footer class="">
        <div class="container">
            <p class="m-0 text-center text-white">Copyright &copy; Mero Calendar 2025</p>
        </div>
    </footer>

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
                const isAuthenticated = <?php echo $isAuthenticated; ?>;
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
                    const today = new Date();
                    const yesterday = new Date(today);
                    yesterday.setDate(yesterday.getDate() - 1);
                    const tomorrow = new Date(today);
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    const nextWeek = new Date(today);
                    nextWeek.setDate(nextWeek.getDate() + 7);
                    
                    // Dummy events and financial data
                    calendarData[today.toDateString()] = [
                        {
                            type: 'income',
                            title: 'Salary Payment',
                            category: 'Salary',
                            subCategory: 'Regular Salary',
                            amount: '3500.00',
                            date: today.toDateString()
                        },
                        {
                            type: 'expense',
                            title: 'Grocery Shopping',
                            category: 'Food',
                            subCategory: 'Groceries',
                            amount: '120.50',
                            date: today.toDateString()
                        },
                        {
                            type: 'event',
                            title: 'Team Meeting',
                            category: 'Work',
                            subCategory: 'Meeting',
                            date: today.toDateString()
                        }
                    ];
                    
                    calendarData[yesterday.toDateString()] = [
                        {
                            type: 'expense',
                            title: 'Gas Station',
                            category: 'Transportation',
                            subCategory: 'Gas',
                            amount: '45.00',
                            date: yesterday.toDateString()
                        },
                        {
                            type: 'expense',
                            title: 'Coffee Shop',
                            category: 'Food',
                            subCategory: 'Restaurants',
                            amount: '8.50',
                            date: yesterday.toDateString()
                        },
                        {
                            type: 'event',
                            title: 'Dentist Appointment',
                            category: 'Health',
                            subCategory: 'Checkup',
                            date: yesterday.toDateString()
                        }
                    ];
                    
                    calendarData[tomorrow.toDateString()] = [
                        {
                            type: 'income',
                            title: 'Freelance Project',
                            category: 'Other',
                            subCategory: 'Freelance',
                            amount: '800.00',
                            date: tomorrow.toDateString()
                        },
                        {
                            type: 'event',
                            title: 'Birthday Party',
                            category: 'Personal',
                            subCategory: 'Birthday',
                            date: tomorrow.toDateString()
                        }
                    ];
                    
                    calendarData[nextWeek.toDateString()] = [
                        {
                            type: 'expense',
                            title: 'Monthly Rent',
                            category: 'Housing',
                            subCategory: 'Rent',
                            amount: '1200.00',
                            date: nextWeek.toDateString()
                        },
                        {
                            type: 'income',
                            title: 'Investment Dividend',
                            category: 'Investment',
                            subCategory: 'Dividends',
                            amount: '150.00',
                            date: nextWeek.toDateString()
                        },
                        {
                            type: 'event',
                            title: 'Conference Call',
                            category: 'Work',
                            subCategory: 'Conference',
                            date: nextWeek.toDateString()
                        }
                    ];
                    // console.log(calendarData)
                }

                // Category data
                const categories = @json(isset($data['categories']) ? $data['categories'] : []);
                // console.log(categories);
                // const categories = {
                //     income: {
                //         'Salary': ['Regular Salary', 'Bonus', 'Overtime'],
                //         'Business': ['Sales', 'Services', 'Investment'],
                //         'Investment': ['Dividends', 'Interest', 'Capital Gains'],
                //         'Other': ['Gift', 'Freelance', 'Misc']
                //     },
                //     expense: {
                //         'Food': ['Groceries', 'Restaurants', 'Snacks'],
                //         'Transportation': ['Gas', 'Public Transport', 'Taxi'],
                //         'Housing': ['Rent', 'Utilities', 'Maintenance'],
                //         'Entertainment': ['Movies', 'Games', 'Travel'],
                //         'Healthcare': ['Medicine', 'Doctor', 'Insurance'],
                //         'Other': ['Clothing', 'Education', 'Misc']
                //     }
                // };

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
                            user_id: 1, // You should get the actual authenticated user ID here
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
                    
                    
                    // Reset form
                    // $('#entryForm')[0].reset();
                    // $('#amountGroup').hide();
                    // $('#entryAmount').prop('required', false);
                    
                    // // Update display
                    // updateTabContent();
                    // generateCalendar();
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
</html>

