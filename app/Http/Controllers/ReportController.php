<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Store a progress report.
     */
    public function storeProgress(Request $request, $projectId)
    {
        // Validate the request
        $validated = $request->validate([
            'report_date' => 'required|date',
            'progress_percentage' => 'required|integer|min:0|max:100',
            'work_completed' => 'required|string',
            'next_steps' => 'required|string',
            'issues' => 'nullable|string',
            'additional_notes' => 'nullable|string',
        ]);

        // In a real application, you would save to database with projectId
        // For now, just redirect back with success message
        return redirect()->back()->with('success', 'Progress report submitted successfully!');
    }

    /**
     * Store documentation.
     */
    public function storeDocumentation(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        // In a real application, you would handle file upload and save to database
        // For now, just redirect back with success message
        return redirect()->back()->with('success', 'Documentation uploaded successfully!');
    }
}