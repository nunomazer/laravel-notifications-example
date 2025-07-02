@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1 class="welcome-message">Welcome, {{ Auth::user()->name }}!</h1>

    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-title">Total of NOtifications</div>
            <div class="stat-value">{{ Auth::user()->notifications()->count() }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Unread</div>
            <div class="stat-value">{{ Auth::user()->unreadNotifications()->count() }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Today</div>
            <div class="stat-value">{{ Auth::user()->notifications()->whereDate('created_at', today())->count() }}</div>
        </div>
    </div>
@endsection