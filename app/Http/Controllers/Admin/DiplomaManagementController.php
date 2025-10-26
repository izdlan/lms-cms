<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\DiplomaLearningOutcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiplomaManagementController extends Controller
{
    public function index()
    {
        $programs = Program::whereIn('level', ['diploma', 'Diploma'])
            ->withCount(['diplomaLearningOutcomes as plo_count'])
            ->orderBy('code')
            ->get();

        return view('admin.diploma.index', compact('programs'));
    }

    public function plos(Program $program)
    {
        $plos = $program->diplomaLearningOutcomes()->ordered()->get();
        
        return view('admin.diploma.plos', compact('program', 'plos'));
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
            'practical_skills' => 'nullable|string',
            'industry_requirements' => 'nullable|string',
            'sort_order' => 'nullable|integer'
        ]);

        $program->diplomaLearningOutcomes()->create([
            'plo_code' => $request->plo_code,
            'description' => $request->description,
            'mqf_domain' => $request->mqf_domain,
            'mqf_code' => $request->mqf_code,
            'mapped_courses' => $request->mapped_courses,
            'assessment_methods' => $request->assessment_methods,
            'practical_skills' => $request->practical_skills,
            'industry_requirements' => $request->industry_requirements,
            'sort_order' => $request->sort_order ?? 0
        ]);

        return redirect()->route('admin.diploma.plos', $program)
            ->with('success', 'Diploma PLO created successfully!');
    }

    public function updatePlo(Request $request, DiplomaLearningOutcome $plo)
    {
        $request->validate([
            'plo_code' => 'required|string|max:10',
            'description' => 'required|string',
            'mqf_domain' => 'required|string|max:50',
            'mqf_code' => 'required|string|max:10',
            'mapped_courses' => 'required|string',
            'assessment_methods' => 'nullable|string',
            'practical_skills' => 'nullable|string',
            'industry_requirements' => 'nullable|string',
            'sort_order' => 'nullable|integer'
        ]);

        $plo->update($request->all());

        return redirect()->route('admin.diploma.plos', $plo->program)
            ->with('success', 'Diploma PLO updated successfully!');
    }

    public function destroyPlo(DiplomaLearningOutcome $plo)
    {
        $program = $plo->program;
        $plo->delete();

        return redirect()->route('admin.diploma.plos', $program)
            ->with('success', 'Diploma PLO deleted successfully!');
    }

    public function extractFromDocument(Request $request, Program $program)
    {
        $request->validate([
            'document' => 'required|file|mimes:doc,docx,pdf|max:10240'
        ]);

        // Placeholder for document extraction logic
        // This would integrate with document parsing libraries
        
        return redirect()->route('admin.diploma.plos', $program)
            ->with('info', 'Document extraction feature will be implemented soon!');
    }
}