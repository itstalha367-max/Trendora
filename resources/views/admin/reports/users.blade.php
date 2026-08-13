@extends('layouts.admin')

@section('title', 'User Report')

@section('content')
<style>
    .report-header { animation: slideDown 0.5s ease-out; }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .stat-box {
        background: #111722;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid rgba(0,0,0,0.04);
        transition: all 0.3s;
    }
    .stat-box:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.06);
    }
    .stat-box .number {
        font-size: 28px;
        font-weight: 800;
        margin: 0;
    }
    .stat-box .label {
        color: #93a1b4;
        font-size: 14px;
        margin: 0;
    }
    .chart-container {
        background: #111722;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid rgba(0,0,0,0.04);
    }
</style>

<div class="report-header">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="fas fa-users me-2 text-warning"></i>User Report</h2>
            <p class="text-muted mb-0">User growth and activity</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.reports') }}" class="btn btn-secondary rounded-3">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
            <select class="form-select w-auto" onchange="window.location.href='?period='+this.value">
                <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Today</option>
                <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>This Week</option>
                <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>This Month</option>
                <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>This Year</option>
            </select>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-box">
                <p class="label">Total Users</p>
                <h3 class="number text-primary">{{ $totalUsers ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <p class="label">New Users ({{ ucfirst(request('period', 'month')) }})</p>
                <h3 class="number text-success">{{ $newUsersCount ?? 0 }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <p class="label">Admins</p>
                <h3 class="number text-warning">{{ App\Models\User::where('role', 'admin')->count() }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <p class="label">Customers</p>
                <h3 class="number text-info">{{ App\Models\User::where('role', 'user')->count() }}</h3>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="chart-container">
        <h5 class="fw-bold mb-3"><i class="fas fa-chart-area me-2 text-primary"></i>User Growth</h5>
        <div style="height: 300px;">
            <canvas id="userChart"></canvas>
        </div>
    </div>

    <!-- User List -->
    <div class="card border-0 shadow-sm rounded-4 mt-4">
        <div class="card-body p-0">
            <div style="overflow-x: auto;">
                <div class="table-responsive"><table class="table table-hover mb-0">
                    <thead style="background: #0f141e;">
                        <tr>
                            <th style="padding: 15px 20px;">ID</th>
                            <th style="padding: 15px 20px;">Name</th>
                            <th style="padding: 15px 20px;">Email</th>
                            <th style="padding: 15px 20px;">Role</th>
                            <th style="padding: 15px 20px;">Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(App\Models\User::latest()->take(10)->get() as $user)
                        <tr>
                            <td style="padding: 15px 20px;">#{{ $user->id }}</td>
                            <td style="padding: 15px 20px;">
                                <div class="d-flex align-items-center">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #8b5cf6, #5b7cff); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; margin-right: 10px;">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    {{ $user->name }}
                                </div>
                            </td>
                            <td style="padding: 15px 20px;">{{ $user->email }}</td>
                            <td style="padding: 15px 20px;">
                                <span class="badge rounded-pill px-3 py-2 {{ $user->role == 'admin' ? 'bg-success' : 'bg-primary' }}">
                                    {{ ucfirst($user->role ?? 'user') }}
                                </span>
                            </td>
                            <td style="padding: 15px 20px;">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No users found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('userChart').getContext('2d');
    
    const labels = @json($newUsers->pluck('date')->map(function($date) {
        return \Carbon\Carbon::parse($date)->format('d M');
    }));
    
    const data = @json($newUsers->pluck('count'));
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'New Users',
                data: data,
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(253, 203, 110, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#f59e0b',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#151d2a',
                    titleColor: '#fff',
                    bodyColor: '#cbd5e1',
                    cornerRadius: 10,
                    padding: 12,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    },
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection