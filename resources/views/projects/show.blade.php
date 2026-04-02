@extends('layouts.app')

@section('title', $project['name'])

@section('content')
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dashboard.projects') }}">Active Projects</a></li>
            <li class="breadcrumb-item active">{{ $project['name'] }}</li>
        </ol>
    </nav>

    <!-- Project Header -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <h1 class="mb-0">{{ $project['name'] }}</h1>
                <span class="badge bg-{{ $project['status'] === 'active' ? 'success' : 'info' }}">{{ ucfirst($project['status']) }}</span>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div>
                        <small class="text-muted">Start Date</small>
                        <p class="fw-semibold">{{ $project['start_date'] }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div>
                        <small class="text-muted">End Date</small>
                        <p class="fw-semibold">{{ $project['end_date'] }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div>
                        <small class="text-muted">Team Members</small>
                        <p class="fw-semibold">{{ $project['team_members'] }} Members</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <ul class="nav nav-tabs mb-4" id="projectTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">Overview</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="installation-tab" data-bs-toggle="tab" data-bs-target="#installation" type="button" role="tab" aria-controls="installation" aria-selected="false">Installation</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="programming-tab" data-bs-toggle="tab" data-bs-target="#programming" type="button" role="tab" aria-controls="programming" aria-selected="false">Programming</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="troubleshooting-tab" data-bs-toggle="tab" data-bs-target="#troubleshooting" type="button" role="tab" aria-controls="troubleshooting" aria-selected="false">Troubleshooting</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="report-progress-tab" data-bs-toggle="tab" data-bs-target="#report-progress" type="button" role="tab" aria-controls="report-progress" aria-selected="false">Report Progress</button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="projectTabsContent">
        <!-- Overview Tab -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
            <div class="row">
                <!-- Progress Overview -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Progress Overview</h5>
                            <div class="space-y-3">
                                @foreach($progressStages as $stage)
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="fw-semibold">{{ $stage['name'] }}</span>
                                        <span class="badge bg-secondary">{{ $stage['percentage'] }}%</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-{{ $stage['color'] === 'orange' ? 'warning' : ($stage['color'] === 'red' ? 'danger' : ($stage['color'] === 'blue' ? 'info' : 'success')) }}" role="progressbar" style="width: {{ $stage['percentage'] }}%"></div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documentation -->
                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Documentation</h5>
                            <div class="row g-2 mb-3">
                                @foreach($documentation as $doc)
                                <div class="col-6">
                                    <div class="text-center">
                                        <img src="{{ $doc['image'] }}" alt="{{ $doc['title'] }}" class="img-fluid rounded mb-2" style="max-height: 80px; object-fit: cover;">
                                        <small class="d-block text-muted">{{ $doc['title'] }}</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <button class="btn btn-sm btn-primary w-100" onclick="window.location.href='{{ route('projects.documentation', $project['id']) }}'">View All</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Installation Tab -->
        <div class="tab-pane fade" id="installation" role="tabpanel" aria-labelledby="installation-tab">
            @include('projects.installation', ['project' => $project, 'tasks' => $installationTasks])
        </div>

        <!-- Programming Tab -->
        <div class="tab-pane fade" id="programming" role="tabpanel" aria-labelledby="programming-tab">
            @include('projects.programming', ['project' => $project, 'tasks' => $programmingTasks])
        </div>

        <!-- Troubleshooting Tab -->
        <div class="tab-pane fade" id="troubleshooting" role="tabpanel" aria-labelledby="troubleshooting-tab">
            @include('projects.troubleshooting', ['project' => $project, 'issues' => $troubleshootingIssues])
        </div>

        <!-- Report Progress Tab -->
        <div class="tab-pane fade" id="report-progress" role="tabpanel" aria-labelledby="report-progress-tab">
            @include('projects.report-progress', ['project' => $project, 'recentReports' => $recentReports])
        </div>
    </div>
@endsection
