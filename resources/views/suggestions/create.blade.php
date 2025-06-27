@extends('layouts.app')

@section('content')

    @if(session('success'))
        <?php 
        echo session('success');
         ?>
        <script>
            toastr.success('{{ session('success') }}');
        </script>
    @endif

    @if(session('error'))
        <script>
            toastr.error('{{ session('error') }}');
        </script>
    @endif

    <form action="{{ url('/feedback') }}" method="POST">
        @csrf
        <label for="name">Your Name</label>
        <input type="text" name="name" required><br><br>

        <label for="message">Your Suggestion</label>
        <textarea name="message" required></textarea><br><br>

        <button type="submit">Submit</button>
    </form>

@endsection