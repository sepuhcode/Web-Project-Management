@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Statistics Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card shadow-sm border-primary">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="text-primary">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                            <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3 class="text-primary mb-2">{{ $stats['active_projects'] }}</h3>
                    <p class="text-muted mb-0">Active Projects</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-success">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="text-success">
                            <rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                            <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3 class="text-success mb-2">{{ $stats['completed_projects'] }}</h3>
                    <p class="text-muted mb-0">Completed Projects</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-warning">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="text-warning">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="currentColor" stroke-width="2"/>
                            <path d="M14 2v6h6" stroke="currentColor" stroke-width="2"/>
                            <path d="M8 13h8M8 17h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3 class="text-warning mb-2">{{ $stats['issues_reported'] }}</h3>
                    <p class="text-muted mb-0">Issues Reported</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Cards -->
    <div class="row g-4">
        <!-- Progress Overview -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">Progress Overview</h5>
                    <div class="mb-3">
                        @foreach($progressData as $project)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold">{{ $project['name'] }}</span>
                                <span class="badge bg-secondary">{{ $project['progress'] }}%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-{{ $project['color'] === 'orange' ? 'warning' : ($project['color'] === 'red' ? 'danger' : ($project['color'] === 'blue' ? 'info' : 'success')) }}" role="progressbar" style="width: {{ $project['progress'] }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <a href="{{ route('dashboard.projects') }}" class="btn btn-primary">View All Projects</a>
                </div>
            </div>
        </div>

        <!-- Recent Issues -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">Recent Issues</h5>
                    <div class="list-group list-group-flush">
                        @foreach($recentIssues as $issue)
                        <div class="list-group-item px-0 border-0">
                            <h6 class="mb-1">{{ $issue['title'] }}</h6>
                            <small class="text-muted">Reported: {{ $issue['date'] }}</small>
                        </div>
                        @endforeach
                    </div>
                    <button class="btn btn-outline-danger w-100 mt-3">View All Issues</button>
                </div>
            </div>
        </div>
    </div>
@endsection
