<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard.
     */
    public function index()
    {
        // Sample data for demonstration
        $stats = [
            'active_projects' => 9,
            'completed_projects' => 5,
            'issues_reported' => 5
        ];

        $progressData = [
            ['name' => 'TBIG BALI', 'progress' => 45, 'color' => 'orange'],
            ['name' => 'MENARA MANDIRI', 'progress' => 85, 'color' => 'green'],
            ['name' => 'JAPFA', 'progress' => 10, 'color' => 'red']
        ];

        $recentIssues = [
            ['title' => 'Audio Feedback MM', 'date' => '2 Januari 2026'],
            ['title' => 'BUG SWITCHING MM', 'date' => '2 Januari 2026'],
            ['title' => 'BUG CAMERA MM', 'date' => '2 Januari 2026']
        ];

        return view('dashboard.index', compact('stats', 'progressData', 'recentIssues'));
    }

    /**
     * Display all projects page.
     */
    public function allProjects()
    {
        $projects = [
            [
                'id' => 1,
                'name' => 'TBIG BALI',
                'status' => 'active',
                'progress' => 45,
                'start_date' => '1 Januari 2026',
                'end_date' => '31 Maret 2026',
                'team_members' => 5,
                'color' => 'orange'
            ],
            [
                'id' => 2,
                'name' => 'MENARA MANDIRI',
                'status' => 'active',
                'progress' => 85,
                'start_date' => '15 Januari 2026',
                'end_date' => '15 April 2026',
                'team_members' => 8,
                'color' => 'green'
            ],
            [
                'id' => 3,
                'name' => 'JAPFA',
                'status' => 'active',
                'progress' => 10,
                'start_date' => '1 Februari 2026',
                'end_date' => '30 Juni 2026',
                'team_members' => 3,
                'color' => 'red'
            ],
            [
                'id' => 4,
                'name' => 'PROJECT ALPHA',
                'status' => 'active',
                'progress' => 60,
                'start_date' => '10 Januari 2026',
                'end_date' => '10 Mei 2026',
                'team_members' => 6,
                'color' => 'blue'
            ],
            [
                'id' => 5,
                'name' => 'PROJECT BETA',
                'status' => 'completed',
                'progress' => 100,
                'start_date' => '1 Desember 2025',
                'end_date' => '15 Januari 2026',
                'team_members' => 4,
                'color' => 'green'
            ],
            [
                'id' => 6,
                'name' => 'PROJECT GAMMA',
                'status' => 'active',
                'progress' => 30,
                'start_date' => '5 Februari 2026',
                'end_date' => '5 Juli 2026',
                'team_members' => 7,
                'color' => 'orange'
            ]
        ];

        $stats = [
            'active' => 9,
            'completed' => 5
        ];

        return view('dashboard.projects', compact('projects', 'stats'));
    }
}
