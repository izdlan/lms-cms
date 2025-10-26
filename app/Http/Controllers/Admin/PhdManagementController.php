<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\PhdLearningOutcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhdManagementController extends Controller
{
    public function index()
    {
        $programs = Program::whereIn('level', ['phd', 'doctorate', 'PhD', 'Doctorate'])
            ->withCount(['phdLearningOutcomes as plo_count'])
            ->orderBy('code')
            ->get();

        return view('admin.phd.index', compact('programs'));
    }

    public function plos(Program $program)
    {
        $plos = $program->phdLearningOutcomes()->ordered()->get();
        
        return view('admin.phd.plos', compact('program', 'plos'));
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
            'original_research' => 'nullable|string',
            'advanced_research_methods' => 'nullable|string',
            'theoretical_contribution' => 'nullable|string',
            'publication_requirements' => 'nullable|string',
            'supervision_skills' => 'nullable|string',
            'dissertation_defense' => 'nullable|string',
            'sort_order' => 'nullable|integer'
        ]);

        $program->phdLearningOutcomes()->create([
            'plo_code' => $request->plo_code,
            'description' => $request->description,
            'mqf_domain' => $request->mqf_domain,
            'mqf_code' => $request->mqf_code,
            'mapped_courses' => $request->mapped_courses,
            'assessment_methods' => $request->assessment_methods,
            'original_research' => $request->original_research,
            'advanced_research_methods' => $request->advanced_research_methods,
            'theoretical_contribution' => $request->theoretical_contribution,
            'publication_requirements' => $request->publication_requirements,
            'supervision_skills' => $request->supervision_skills,
            'dissertation_defense' => $request->dissertation_defense,
            'sort_order' => $request->sort_order ?? 0
        ]);

        return redirect()->route('admin.phd.plos', $program)
            ->with('success', 'PhD PLO created successfully!');
    }

    public function updatePlo(Request $request, PhdLearningOutcome $plo)
    {
        $request->validate([
            'plo_code' => 'required|string|max:10',
            'description' => 'required|string',
            'mqf_domain' => 'required|string|max:50',
            'mqf_code' => 'required|string|max:10',
            'mapped_courses' => 'required|string',
            'assessment_methods' => 'nullable|string',
            'original_research' => 'nullable|string',
            'advanced_research_methods' => 'nullable|string',
            'theoretical_contribution' => 'nullable|string',
            'publication_requirements' => 'nullable|string',
            'supervision_skills' => 'nullable|string',
            'dissertation_defense' => 'nullable|string',
            'sort_order' => 'nullable|integer'
        ]);

        $plo->update($request->all());

        return redirect()->route('admin.phd.plos', $plo->program)
            ->with('success', 'PhD PLO updated successfully!');
    }

    public function destroyPlo(PhdLearningOutcome $plo)
    {
        $program = $plo->program;
        $plo->delete();

        return redirect()->route('admin.phd.plos', $program)
            ->with('success', 'PhD PLO deleted successfully!');
    }

    public function extractFromDocument(Request $request, Program $program)
    {
        $request->validate([
            'document' => 'required|file|mimes:doc,docx,pdf|max:10240'
        ]);

        // Placeholder for document extraction logic
        // This would integrate with document parsing libraries
        
        return redirect()->route('admin.phd.plos', $program)
            ->with('info', 'Document extraction feature will be implemented soon!');
    }
}