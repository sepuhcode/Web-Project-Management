<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display the specified project details.
     */
    public function show($id)
    {
        // Sample project data
        $project = [
            'id' => $id,
            'name' => 'Tower Bersama Indonesia Group - Bali',
            'status' => 'active',
            'start_date' => '1 Januari 2026',
            'end_date' => '31 Maret 2026',
            'team_members' => 5
        ];

        $progressStages = [
            ['name' => 'Survey', 'percentage' => 100, 'color' => 'green'],
            ['name' => 'Installation', 'percentage' => 85, 'color' => 'orange'],
            ['name' => 'Programming', 'percentage' => 10, 'color' => 'red'],
            ['name' => 'Testing', 'percentage' => 0, 'color' => 'gray'],
            ['name' => 'Training', 'percentage' => 0, 'color' => 'gray']
        ];

        $documentation = [
            ['title' => 'Survey Report', 'image' => 'https://via.placeholder.com/300x200/4A90E2/FFFFFF?text=Survey+Report'],
            ['title' => 'Installation Guide', 'image' => 'https://via.placeholder.com/300x200/4CAF50/FFFFFF?text=Installation+Guide'],
            ['title' => 'Technical Specifications', 'image' => 'https://via.placeholder.com/300x200/FF9800/FFFFFF?text=Technical+Specs'],
            ['title' => 'Project Plan', 'image' => 'https://via.placeholder.com/300x200/E74C3C/FFFFFF?text=Project+Plan']
        ];

        // Installation tasks
        $installationTasks = [
            ['no' => 1, 'task' => 'Pulling Belden cat6 cable from IT Server to Credenza', 'area' => 'BOC', 'weight' => '100%', 'status' => 'completed'],
            ['no' => 2, 'task' => 'Pulling Belden cat6 cable from IT Server to Credenza', 'area' => 'BOC', 'weight' => '50%', 'status' => 'in-progress'],
            ['no' => 3, 'task' => 'Pulling Belden cat6 cable from IT Server to Credenza', 'area' => 'BOC', 'weight' => '50%', 'status' => 'in-progress'],
            ['no' => 4, 'task' => 'Pulling Belden cat6 cable from IT Server to Credenza', 'area' => 'BOC', 'weight' => '50%', 'status' => 'pending']
        ];

        // Programming tasks
        $programmingTasks = [
            ['no' => 1, 'task' => 'Configure Access Control System', 'area' => 'Main Entrance', 'weight' => '30%', 'status' => 'completed'],
            ['no' => 2, 'task' => 'Setup CCTV Network Configuration', 'area' => 'Building A', 'weight' => '25%', 'status' => 'in-progress'],
            ['no' => 3, 'task' => 'Program Smart Lighting System', 'area' => 'Office Area', 'weight' => '20%', 'status' => 'pending'],
            ['no' => 4, 'task' => 'Integration Testing with Building Management', 'area' => 'All Areas', 'weight' => '25%', 'status' => 'pending']
        ];

        // Troubleshooting issues
        $troubleshootingIssues = [
            ['no' => 1, 'issue' => 'Access Control System Not Responding', 'area' => 'Main Entrance', 'priority' => 'high', 'status' => 'in-progress', 'date' => '15 Jan 2026'],
            ['no' => 2, 'issue' => 'CCTV Camera Connection Lost', 'area' => 'Building A', 'priority' => 'medium', 'status' => 'completed', 'date' => '10 Jan 2026'],
            ['no' => 3, 'issue' => 'Smart Lighting Flickering Issue', 'area' => 'Office Area', 'priority' => 'low', 'status' => 'pending', 'date' => '20 Jan 2026'],
            ['no' => 4, 'issue' => 'Network Connectivity Issues', 'area' => 'Server Room', 'priority' => 'high', 'status' => 'in-progress', 'date' => '18 Jan 2026']
        ];

        // Recent reports
        $recentReports = [
            ['date' => '20 Jan 2026, 5:30 PM', 'progress' => 85, 'work_completed' => 'Installation progress updated to 85%. Programming phase initiated.'],
            ['date' => '17 Jan 2026, 3:15 PM', 'progress' => 75, 'work_completed' => 'Major milestone achieved: Installation phase 85% complete.']
        ];

        return view('projects.show', compact('project', 'progressStages', 'documentation', 'installationTasks', 'programmingTasks', 'troubleshootingIssues', 'recentReports'));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit($id)
    {
        $project = [
            'id' => $id,
            'name' => 'Tower Bersama Indonesia Group - Bali',
            'status' => 'active',
            'start_date' => '2026-01-01',
            'end_date' => '2026-03-31',
            'team_size' => 5,
            'progress' => 45,
            'description' => 'Installation and integration of smart building systems for Tower Bersama Indonesia Group - Bali location.',
            'survey_progress' => 100,
            'installation_progress' => 85,
            'programming_progress' => 10,
            'testing_progress' => 0,
            'training_progress' => 0
        ];

        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified project.
     */
    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'project-name' => 'required|string|max:255',
            'project-status' => 'required|in:active,completed,on-hold',
            'start-date' => 'required|date',
            'end-date' => 'required|date|after:start-date',
            'team-size' => 'required|integer|min:1',
            'project-progress' => 'required|integer|min:0|max:100',
            'project-description' => 'nullable|string',
            'survey-progress' => 'nullable|integer|min:0|max:100',
            'installation-progress' => 'nullable|integer|min:0|max:100',
            'programming-progress' => 'nullable|integer|min:0|max:100',
            'testing-progress' => 'nullable|integer|min:0|max:100',
            'training-progress' => 'nullable|integer|min:0|max:100'
        ]);

        // In a real application, you would update the database here
        // For now, we'll just store in session and redirect back with success message
        session()->flash('success', 'Project updated successfully!');

        return redirect()->route('projects.show', $id);
    }

    /**
     * Display installation tasks for the project.
     */
    public function installation($id)
    {
        $project = [
            'id' => $id,
            'name' => 'Tower Bersama Indonesia Group - Bali',
            'status' => 'active',
            'start_date' => '1 Januari 2026',
            'end_date' => '31 Maret 2026',
            'team_members' => 5
        ];

        $tasks = [
            ['no' => 1, 'task' => 'Pulling Belden cat6 cable from IT Server to Credenza', 'area' => 'BOC', 'weight' => '100%', 'status' => 'completed'],
            ['no' => 2, 'task' => 'Pulling Belden cat6 cable from IT Server to Credenza', 'area' => 'BOC', 'weight' => '50%', 'status' => 'in-progress'],
            ['no' => 3, 'task' => 'Pulling Belden cat6 cable from IT Server to Credenza', 'area' => 'BOC', 'weight' => '50%', 'status' => 'in-progress'],
            ['no' => 4, 'task' => 'Pulling Belden cat6 cable from IT Server to Credenza', 'area' => 'BOC', 'weight' => '50%', 'status' => 'pending']
        ];

        return view('projects.installation', compact('project', 'tasks'));
    }

    /**
     * Display programming tasks for the project.
     */
    public function programming($id)
    {
        $project = [
            'id' => $id,
            'name' => 'Tower Bersama Indonesia Group - Bali',
            'status' => 'active',
            'start_date' => '1 Januari 2026',
            'end_date' => '31 Maret 2026',
            'team_members' => 5
        ];

        $tasks = [
            ['no' => 1, 'task' => 'Configure Access Control System', 'area' => 'Main Entrance', 'weight' => '30%', 'status' => 'completed'],
            ['no' => 2, 'task' => 'Setup CCTV Network Configuration', 'area' => 'Building A', 'weight' => '25%', 'status' => 'in-progress'],
            ['no' => 3, 'task' => 'Program Smart Lighting System', 'area' => 'Office Area', 'weight' => '20%', 'status' => 'pending'],
            ['no' => 4, 'task' => 'Integration Testing with Building Management', 'area' => 'All Areas', 'weight' => '25%', 'status' => 'pending']
        ];

        return view('projects.programming', compact('project', 'tasks'));
    }

    /**
     * Display troubleshooting issues for the project.
     */
    public function troubleshooting($id)
    {
        $project = [
            'id' => $id,
            'name' => 'Tower Bersama Indonesia Group - Bali',
            'status' => 'active',
            'start_date' => '1 Januari 2026',
            'end_date' => '31 Maret 2026',
            'team_members' => 5
        ];

        $issues = [
            ['no' => 1, 'issue' => 'Access Control System Not Responding', 'area' => 'Main Entrance', 'priority' => 'high', 'status' => 'in-progress', 'assigned_to' => 'John Doe', 'date' => '15 Jan 2026'],
            ['no' => 2, 'issue' => 'CCTV Camera Connection Lost', 'area' => 'Building A', 'priority' => 'medium', 'status' => 'completed', 'assigned_to' => 'Jane Smith', 'date' => '10 Jan 2026'],
            ['no' => 3, 'issue' => 'Smart Lighting Flickering Issue', 'area' => 'Office Area', 'priority' => 'low', 'status' => 'pending', 'assigned_to' => 'Bob Johnson', 'date' => '20 Jan 2026'],
            ['no' => 4, 'issue' => 'Network Connectivity Issues', 'area' => 'Server Room', 'priority' => 'high', 'status' => 'in-progress', 'assigned_to' => 'Alice Brown', 'date' => '18 Jan 2026']
        ];

        return view('projects.troubleshooting', compact('project', 'issues'));
    }

    /**
     * Display report progress form for the project.
     */
    public function reportProgress($id)
    {
        $project = [
            'id' => $id,
            'name' => 'Tower Bersama Indonesia Group - Bali',
            'status' => 'active',
            'start_date' => '1 Januari 2026',
            'end_date' => '31 Maret 2026',
            'team_members' => 5
        ];

        $recentReports = [
            ['date' => '20 Jan 2026, 5:30 PM', 'progress' => 85, 'work_completed' => 'Installation progress updated to 85%. Programming phase initiated.'],
            ['date' => '17 Jan 2026, 3:15 PM', 'progress' => 75, 'work_completed' => 'Major milestone achieved: Installation phase 85% complete.']
        ];

        return view('projects.report-progress', compact('project', 'recentReports'));
    }

    /**
     * Display documentation upload page for the project.
     */
    public function documentation($id)
    {
        $project = [
            'id' => $id,
            'name' => 'Tower Bersama Indonesia Group - Bali'
        ];

        $documentation = [
            ['title' => 'Survey Report', 'image' => 'https://via.placeholder.com/300x200/4A90E2/FFFFFF?text=Survey+Report', 'description' => 'Initial site survey documentation'],
            ['title' => 'Installation Guide', 'image' => 'https://via.placeholder.com/300x200/4CAF50/FFFFFF?text=Installation+Guide', 'description' => 'Step-by-step installation process'],
            ['title' => 'Technical Specifications', 'image' => 'https://via.placeholder.com/300x200/FF9800/FFFFFF?text=Technical+Specs', 'description' => 'System technical documentation'],
            ['title' => 'Project Plan', 'image' => 'https://via.placeholder.com/300x200/E74C3C/FFFFFF?text=Project+Plan', 'description' => 'Overall project timeline and milestones']
        ];

        return view('projects.documentation', compact('project', 'documentation'));
    }
}
