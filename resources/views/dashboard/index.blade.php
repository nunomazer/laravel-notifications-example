@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="dashboard-container">
        @livewire('dashboard')
    </div>

    <style>
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }

        .filters-section {
            background: white;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-label {
            font-size: 14px;
            font-weight: 500;
            color: #374151;
        }

        .filter-input {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s;
        }

        .filter-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .filter-button {
            padding: 8px 16px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .filter-button:hover {
            background: #2563eb;
        }

        .filter-button.secondary {
            background: #6b7280;
        }

        .filter-button.secondary:hover {
            background: #4b5563;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #3b82f6;
        }

        .stat-card.success {
            border-left-color: #10b981;
        }

        .stat-card.warning {
            border-left-color: #f59e0b;
        }

        .stat-card.error {
            border-left-color: #ef4444;
        }

        .stat-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 16px;
        }

        .stat-title {
            font-size: 14px;
            font-weight: 500;
            color: #6b7280;
            margin: 0;
        }

        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #111827;
            margin: 0;
        }

        .stat-subtitle {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 4px;
        }

        .chart-section {
            background: white;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .chart-title {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }

        .period-selector {
            display: flex;
            gap: 8px;
        }

        .period-button {
            padding: 6px 12px;
            font-size: 12px;
            border: 1px solid #d1d5db;
            background: white;
            color: #6b7280;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .period-button.active {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        .chart-container {
            height: 300px;
            position: relative;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #f3f4f6;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        .insights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .insight-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .insight-title {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 12px;
        }

        .type-stats {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .type-stat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .type-stat-item:last-child {
            border-bottom: none;
        }

        .type-name {
            font-weight: 500;
            color: #374151;
        }

        .type-count {
            font-weight: 600;
            color: #111827;
        }
    </style>
@endsection

