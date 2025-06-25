@extends('layouts.app')

@section('content')

        <div class="header">
            <a href="{{route('home')}}"><h1>Kharcha App</h1></a> 

            <!-- Hamburger Button -->
            <button class="hamburger" id="hamburger-btn">
                &#9776; <!-- Unicode for the hamburger icon (three bars) -->
            </button>

        </div>
            <div class="order-lg-1 order-1">
                <div class="date-controls">
                    <button class="nav-arrow" id="prevMonth">‹</button>
                    <div class="date-selectors">
                        <select id="monthSelect">
                            <option value="0">January</option>
                            <option value="1">February</option>
                            <option value="2">March</option>
                            <option value="3">April</option>
                            <option value="4">May</option>
                            <option value="5">June</option>
                            <option value="6">July</option>
                            <option value="7">August</option>
                            <option value="8">September</option>
                            <option value="9">October</option>
                            <option value="10">November</option>
                            <option value="11">December</option>
                        </select>
                        <select id="yearSelect"></select>
                    </div>
                    <button class="nav-arrow" id="nextMonth">›</button>
                </div>

                <div class="calendar-container">
                    <table class="calendar" id="calendar">
                        <thead>
                            <tr>
                                <th>Sun</th>
                                <th>Mon</th>
                                <th>Tue</th>
                                <th>Wed</th>
                                <th>Thu</th>
                                <th>Fri</th>
                                <th>Sat</th>
                            </tr>
                        </thead>
                        <tbody id="calendarBody">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="order-lg-2 order-2">
                <div class="tabs-container">
                    <div class="current-date" id="currentDateDisplay"></div>
                    <div class="tabs">
                        <button class="tab active" data-tab="transactions">Transactions</button>
                        <button class="tab" data-tab="events">Events</button>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane active" id="transactions">
                            {{-- <h3>Financial Info for Selected Date</h3> --}}
                            @guest
                                <div id="auth-required">
                                    <p>You need to 
                                       <a href="{{route('login')}}">Login</a> or  <a href="{{route('register')}}">Register</a> to see your transactions</p>
                                </div>
                            @endguest
                            @auth
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                </a>

                                <div id="infoList"></div>
                            @endauth
                        </div>
                        <div class="tab-pane " id="events">
                            {{-- <h3>Events for Selected Date</h3> --}}
                            <div id="eventsList"></div>
                        </div>
                    </div>
                </div>
            </div>
        {{-- </div> --}}

@endsection
