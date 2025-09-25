@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('content')
<div class="staff-dashboard">
    <!-- Staff Navigation Bar -->
    @include('staff.partials.staff-navbar')
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            @include('staff.partials.sidebar')

            <!-- Main Content -->
            <div class="main-content">
                @yield('content')
            </div>
        </div>
    </div>
</div>
@endsection
