<!-- Report Progress Content -->
<div class="row">
<!-- Progress Report Form -->
<div class="col-lg-8 mb-4">
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">Submit Progress Report</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.progress.store', $project['id']) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Basic Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="report_date" class="form-label">Report Date</label>
                            <input type="date" class="form-control" id="report_date" name="report_date" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="report_type" class="form-label">Report Type</label>
                            <select id="report_type" name="report_type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="daily">Daily Report</option>
                                <option value="weekly">Weekly Report</option>
                                <option value="milestone">Milestone Report</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Summary Fields -->
                <div class="mb-3">
                    <label for="progress_summary" class="form-label">Progress Summary</label>
                    <textarea class="form-control" id="progress_summary" name="progress_summary" rows="4" placeholder="Describe the progress made..." required></textarea>
                </div>

                <div class="mb-3">
                    <label for="issues_faced" class="form-label">Issues Faced</label>
                    <textarea class="form-control" id="issues_faced" name="issues_faced" rows="3" placeholder="Any issues or challenges encountered..."></textarea>
                </div>

                <div class="mb-3">
                    <label for="next_steps" class="form-label">Next Steps</label>
                    <textarea class="form-control" id="next_steps" name="next_steps" rows="3" placeholder="Planned activities for next period..."></textarea>
                </div>

                <!-- Stage Progress Updates -->
                <div class="card mb-4 border">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Stage Progress Updates</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label for="survey_progress" class="form-label">Survey (%)</label>
                                <input type="number" class="form-control" id="survey_progress" name="survey_progress" min="0" max="100" value="100">
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label for="installation_progress" class="form-label">Installation (%)</label>
                                <input type="number" class="form-control" id="installation_progress" name="installation_progress" min="0" max="100" value="85">
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label for="programming_progress" class="form-label">Programming (%)</label>
                                <input type="number" class="form-control" id="programming_progress" name="programming_progress" min="0" max="100" value="10">
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label for="testing_progress" class="form-label">Testing (%)</label>
                                <input type="number" class="form-control" id="testing_progress" name="testing_progress" min="0" max="100" value="0">
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label for="training_progress" class="form-label">Training (%)</label>
                                <input type="number" class="form-control" id="training_progress" name="training_progress" min="0" max="100" value="0">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documentation Upload -->
                <div class="card mb-4 border">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Documentation Upload</h6>
                    </div>
                    <div class="card-body">
                        <div class="border-2 border-dashed rounded p-4 text-center" style="border: 2px dashed #ddd;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" class="mb-3 mx-auto d-block" style="color: #3498db;">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <polyline points="17,8 12,3 7,8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <line x1="12" y1="3" x2="12" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            <p class="mb-1">Drag and drop images here or click to browse</p>
                            <p class="small text-muted mb-3">Supported formats: JPG, PNG, PDF (Max 5MB)</p>
                            <input type="file" id="file_upload" name="files[]" class="d-none" multiple accept="image/*,.pdf">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="document.getElementById('file_upload').click()">Choose Files</button>
                        </div>
                        <div id="uploaded-files" class="mt-3"></div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Submit Report</button>
                    <button type="button" class="btn btn-secondary">Save as Draft</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Recent Reports -->
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Recent Reports</h5>
            </div>
            <div class="card-body">
                @if(isset($recentReports) && count($recentReports) > 0)
                    @foreach($recentReports as $report)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <small class="text-muted">{{ $report['date'] }}</small>
                            <span class="badge bg-primary">{{ $report['progress'] }}%</span>
                        </div>
                        <p class="mb-1 small">{{ Str::limit($report['work_completed'], 100) }}</p>
                        <button class="btn btn-sm btn-outline-primary">View Details</button>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted mb-0">No recent reports available.</p>
                @endif
            </div>
        </div>
    </div>
</div>