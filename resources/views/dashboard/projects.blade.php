@extends('layouts.app')

@section('title', 'All Projects')

@section('content')
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">Home</a></li>
            <li class="breadcrumb-item active">All Projects</li>
        </ol>
    </nav>

    <!-- Projects Header -->
    <div class="mb-5">
        <h1 class="mb-4">All Projects</h1>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-primary mb-1">{{ $stats['active'] }}</h3>
                        <p class="text-muted mb-0">Active</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-success mb-1">{{ $stats['completed'] }}</h3>
                        <p class="text-muted mb-0">Completed</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Grid -->
    <div class="row g-4">
        @foreach($projects as $project)
        <div class="col-lg-6 col-xl-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="card-title mb-0">{{ $project['name'] }}</h5>
                        <span class="badge bg-{{ $project['status'] === 'active' ? 'success' : 'info' }}">{{ ucfirst($project['status']) }}</span>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <small class="text-muted">Progress</small>
                            <small class="fw-bold">{{ $project['progress'] }}%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-{{ $project['color'] === 'orange' ? 'warning' : ($project['color'] === 'red' ? 'danger' : ($project['color'] === 'blue' ? 'info' : 'success')) }}" role="progressbar" style="width: {{ $project['progress'] }}%"></div>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="small">
                        <div class="mb-2"><strong>Start Date:</strong> {{ $project['start_date'] }}</div>
                        <div class="mb-2"><strong>End Date:</strong> {{ $project['end_date'] }}</div>
                        <div class="mb-3"><strong>Team:</strong> {{ $project['team_members'] }} Members</div>
                    </div>
                </div>
                <div class="card-footer bg-light d-flex gap-2">
                    <a href="{{ route('projects.show', $project['id']) }}" class="btn btn-sm btn-primary">View Details</a>
                    <a href="{{ route('projects.edit', $project['id']) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endsection
