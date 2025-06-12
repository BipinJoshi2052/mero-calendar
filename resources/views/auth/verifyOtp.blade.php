
@extends('layouts.app')

@section('content')
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">{{ __('Verify OTP') }}</div>

            <div class="card-body">
                <h3>Verify Your OTP</h3>
                <p>An OTP has been sent to {{ session('email') }}. Please enter the OTP below to verify your email.</p>
                
                <!-- Display error message if OTP verification fails -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('otp.verify') }}">
                    @csrf
                    <label for="otp">Enter OTP:</label>
                    <input type="text" id="otp" name="otp" required>
                    <button class="btn btn-primary" type="submit">Verify OTP</button>
                </form>
            </div>
        </div>
    </div>
@endsection