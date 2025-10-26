<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\MasterLearningOutcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterManagementController extends Controller
{
    public function index()
    {
        $programs = Program::whereIn('level', ['master', 'masters', 'Master', 'Masters'])
            ->withCount(['masterLearningOutcomes as plo_count'])
            ->orderBy('code')
            ->get();

        return view('admin.master.index', compact('programs'));
    }

    public function plos(Program $program)
    {
        $plos = $program->masterLearningOutcomes()->ordered()->get();
        
        return view('admin.master.plos', compact('program', 'plos'));
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
            'advanced_knowledge' => 'nullable|string',
            'research_methodology' => 'nullable|string',
            'leadership_skills' => 'nullable|string',
            'critical_thinking' => 'nullable|string',
            'dissertation_requirements' => 'nullable|string',
            'sort_order' => 'nullable|integer'
        ]);

        $program->masterLearningOutcomes()->create([
            'plo_code' => $request->plo_code,
            'description' => $request->description,
            'mqf_domain' => $request->mqf_domain,
            'mqf_code' => $request->mqf_code,
            'mapped_courses' => $request->mapped_courses,
            'assessment_methods' => $request->assessment_methods,
            'advanced_knowledge' => $request->advanced_knowledge,
            'research_methodology' => $request->research_methodology,
            'leadership_skills' => $request->leadership_skills,
            'critical_thinking' => $request->critical_thinking,
            'dissertation_requirements' => $request->dissertation_requirements,
            'sort_order' => $request->sort_order ?? 0
        ]);

        return redirect()->route('admin.master.plos', $program)
            ->with('success', 'Master PLO created successfully!');
    }

    public function updatePlo(Request $request, MasterLearningOutcome $plo)
    {
        $request->validate([
            'plo_code' => 'required|string|max:10',
            'description' => 'required|string',
            'mqf_domain' => 'required|string|max:50',
            'mqf_code' => 'required|string|max:10',
            'mapped_courses' => 'required|string',
            'assessment_methods' => 'nullable|string',
            'advanced_knowledge' => 'nullable|string',
            'research_methodology' => 'nullable|string',
            'leadership_skills' => 'nullable|string',
            'critical_thinking' => 'nullable|string',
            'dissertation_requirements' => 'nullable|string',
            'sort_order' => 'nullable|integer'
        ]);

        $plo->update($request->all());

        return redirect()->route('admin.master.plos', $plo->program)
            ->with('success', 'Master PLO updated successfully!');
    }

    public function destroyPlo(MasterLearningOutcome $plo)
    {
        $program = $plo->program;
        $plo->delete();

        return redirect()->route('admin.master.plos', $program)
            ->with('success', 'Master PLO deleted successfully!');
    }

    public function extractFromDocument(Request $request, Program $program)
    {
        $request->validate([
            'document' => 'required|file|mimes:doc,docx,pdf|max:10240'
        ]);

        // Placeholder for document extraction logic
        // This would integrate with document parsing libraries
        
        return redirect()->route('admin.master.plos', $program)
            ->with('info', 'Document extraction feature will be implemented soon!');
    }
}