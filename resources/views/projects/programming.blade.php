<!-- Programming Content -->
<div class="card shadow-sm">
    <div class="card-body">
        <div class="mb-4">
            <h2 class="mb-2">Programming Tasks</h2>
            <p class="text-muted">Development work items and coding progress tracking</p>
        </div>

        <!-- Programming Table -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>LIST PEKERJAAN</th>
                        <th>AREA</th>
                        <th>BOBOT</th>
                        <th>STATUS</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                    <tr>
                        <td>{{ $task['no'] }}</td>
                        <td>{{ $task['task'] }}</td>
                        <td><span class="badge bg-secondary">{{ $task['area'] }}</span></td>
                        <td>{{ $task['weight'] }}</td>
                        <td>
                            <span class="badge bg-{{ $task['status'] === 'completed' ? 'success' : ($task['status'] === 'in-progress' ? 'warning' : 'secondary') }}">
                                {{ ucfirst(str_replace('-', ' ', $task['status'])) }}
                            </span>
                        </td>
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