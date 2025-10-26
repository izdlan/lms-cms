<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\DegreeLearningOutcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DegreeManagementController extends Controller
{
    public function index()
    {
        $programs = Program::whereIn('level', ['degree', 'bachelor', 'Degree', 'Bachelor'])
            ->withCount(['degreeLearningOutcomes as plo_count'])
            ->orderBy('code')
            ->get();

        return view('admin.degree.index', compact('programs'));
    }

    public function plos(Program $program)
    {
        $plos = $program->degreeLearningOutcomes()->ordered()->get();
        
        return view('admin.degree.plos', compact('program', 'plos'));
    }

    public function storePlo(Request $request, Program $program)
    {
        $request->validate([
            'plo_code' => 'required|string|max:10',
            'description' => 'required|string',
            'mqf_domain' => 'required|string|max:50',
            'mqf_code' => 'required|string|max:10',
            'mapped_courses' => 'required|string',
            'assessment_methods' => 'nullable|string',
            'theoretical_foundation' => 'nullable|string',
            'research_skills' => 'nullable|string',
            'professional_competencies' => 'nullable|string',
            'sort_order' => 'nullable|integer'
        ]);

        $program->degreeLearningOutcomes()->create([
            'plo_code' => $request->plo_code,
            'description' => $request->description,
            'mqf_domain' => $request->mqf_domain,
            'mqf_code' => $request->mqf_code,
            'mapped_courses' => $request->mapped_courses,
            'assessment_methods' => $request->assessment_methods,
            'theoretical_foundation' => $request->theoretical_foundation,
            'research_skills' => $request->research_skills,
            'professional_competencies' => $request->professional_competencies,
            'sort_order' => $request->sort_order ?? 0
        ]);

        return redirect()->route('admin.degree.plos', $program)
            ->with('success', 'Degree PLO created successfully!');
    }

    public function updatePlo(Request $request, DegreeLearningOutcome $plo)
    {
        $request->validate([
            'plo_code' => 'required|string|max:10',
            'description' => 'required|string',
            'mqf_domain' => 'required|string|max:50',
            'mqf_code' => 'required|string|max:10',
            'mapped_courses' => 'required|string',
            'assessment_methods' => 'nullable|string',
            'theoretical_foundation' => 'nullable|string',
            'research_skills' => 'nullable|string',
            'professional_competencies' => 'nullable|string',
            'sort_order' => 'nullable|integer'
        ]);

        $plo->update($request->all());

        return redirect()->route('admin.degree.plos', $plo->program)
            ->with('success', 'Degree PLO updated successfully!');
    }

    public function destroyPlo(DegreeLearningOutcome $plo)
    {
        $program = $plo->program;
        $plo->delete();

        return redirect()->route('admin.degree.plos', $program)
            ->with('success', 'Degree PLO deleted successfully!');
    }

    public function extractFromDocument(Request $request, Program $program)
    {
        $request->validate([
            'document' => 'required|file|mimes:doc,docx,pdf|max:10240'
        ]);

        // Placeholder for document extraction logic
        // This would integrate with document parsing libraries
        
        return redirect()->route('admin.degree.plos', $program)
            ->with('info', 'Document extraction feature will be implemented soon!');
    }
}