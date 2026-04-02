<!-- Troubleshooting Content -->
<div class="card shadow-sm">
    <div class="card-body">
        <div class="mb-4">
            <h2 class="mb-2">Troubleshooting Issues</h2>
            <p class="text-muted">Technical issues, bugs, and resolution tracking</p>
        </div>

        <!-- Troubleshooting Table -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>ISSUE</th>
                        <th>PRIORITY</th>
                        <th>STATUS</th>
                        <th>ASSIGNED TO</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($issues as $issue)
                    <tr>
                        <td>{{ $issue['no'] }}</td>
                        <td>{{ $issue['issue'] }}</td>
                        <td>
                            <span class="badge bg-{{ $issue['priority'] === 'high' ? 'danger' : ($issue['priority'] === 'medium' ? 'warning' : 'info') }}">
                                {{ ucfirst($issue['priority']) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $issue['status'] === 'resolved' ? 'success' : ($issue['status'] === 'in-progress' ? 'warning' : 'secondary') }}">
                                {{ ucfirst(str_replace('-', ' ', $issue['status'])) }}
                            </span>
                        </td>
                        <td>{{ $issue['assigned_to'] ?? 'Unassigned' }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-1">View</button>
                            <button class="btn btn-sm btn-outline-secondary">Edit</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>