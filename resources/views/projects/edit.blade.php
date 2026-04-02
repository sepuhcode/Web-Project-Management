@extends('layouts.app')

@section('title', 'Edit Project - ' . $project['name'])

@section('content')
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dashboard.projects') }}">All Projects</a></li>
            <li class="breadcrumb-item active">Edit Project</li>
        </ol>
    </nav>

    <!-- Edit Form -->
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h2 class="card-title mb-4">Edit Project Details</h2>
            <form method="POST" action="{{ route('projects.update', $project['id']) }}">
                @csrf
                <!-- Basic Info Section -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="project-name" class="form-label">Project Name</label>
                            <input type="text" class="form-control" id="project-name" name="project-name" value="{{ $project['name'] }}" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="project-status" class="form-label">Status</label>
                            <select class="form-select" id="project-status" name="project-status">
                                <option value="active" {{ $project['status'] == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ $project['status'] == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="on-hold">On Hold</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Dates Section -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="start-date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start-date" name="start-date" value="{{ $project['start_date'] }}" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="end-date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end-date" name="end-date" value="{{ $project['end_date'] }}" required>
                        </div>
                    </div>
                </div>

                <!-- Team & Progress Section -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="team-size" class="form-label">Team Size</label>
                            <input type="number" class="form-control" id="team-size" name="team-size" value="{{ $project['team_size'] }}" min="1" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="project-progress" class="form-label">Overall Progress (%)</label>
                            <input type="number" class="form-control" id="project-progress" name="project-progress" value="{{ $project['progress'] }}" min="0" max="100" required>
                        </div>
                    </div>
                </div>
                
                <!-- Description Section -->
                <div class="mb-4">
                    <label for="project-description" class="form-label">Project Description</label>
                    <textarea class="form-control" id="project-description" name="project-description" rows="4" placeholder="Enter project description...">{{ $project['description'] }}</textarea>
                </div>
                
                <!-- Stage Progress Section -->
                <div class="mb-4">
                    <h4 class="mb-3">Stage Progress</h4>
                    <div class="row">
                        <div class="col-md-6 col-lg-4">
                            <div class="mb-3">
                                <label for="survey-progress" class="form-label">Survey (%)</label>
                                <input type="number" class="form-control" id="survey-progress" name="survey-progress" value="{{ $project['survey_progress'] }}" min="0" max="100">
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-lg-4">
                            <div class="mb-3">
                                <label for="installation-progress" class="form-label">Installation (%)</label>
                                <input type="number" class="form-control" id="installation-progress" name="installation-progress" value="{{ $project['installation_progress'] }}" min="0" max="100">
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-lg-4">
                            <div class="mb-3">
                                <label for="programming-progress" class="form-label">Programming (%)</label>
                                <input type="number" class="form-control" id="programming-progress" name="programming-progress" value="{{ $project['programming_progress'] }}" min="0" max="100">
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-lg-4">
                            <div class="mb-3">
                                <label for="testing-progress" class="form-label">Testing (%)</label>
                                <input type="number" class="form-control" id="testing-progress" name="testing-progress" value="{{ $project['testing_progress'] }}" min="0" max="100">
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-lg-4">
                            <div class="mb-3">
                                <label for="training-progress" class="form-label">Training (%)</label>
                                <input type="number" class="form-control" id="training-progress" name="training-progress" value="{{ $project['training_progress'] }}" min="0" max="100">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
@endsection
