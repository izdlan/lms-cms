<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\ProgramLearningOutcome;
use App\Models\CourseLearningOutcome;
use App\Models\ProgramSubject;
use App\Models\DiplomaLearningOutcome;
use App\Models\DegreeLearningOutcome;
use App\Models\MasterLearningOutcome;
use App\Models\PhdLearningOutcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class ProgramManagementController extends Controller
{
    /**
     * Display a listing of programs grouped by academic level
     */
    public function index()
    {
        $diplomaPrograms = Program::whereIn('level', ['diploma', 'Diploma'])
            ->withCount(['diplomaLearningOutcomes as plo_count'])
            ->orderBy('code')
            ->get();

        $degreePrograms = Program::whereIn('level', ['degree', 'bachelor', 'Degree', 'Bachelor'])
            ->withCount(['degreeLearningOutcomes as plo_count'])
            ->orderBy('code')
            ->get();

        $masterPrograms = Program::whereIn('level', ['master', 'masters', 'Master', 'Masters'])
            ->withCount(['masterLearningOutcomes as plo_count'])
            ->orderBy('code')
            ->get();

        $phdPrograms = Program::whereIn('level', ['phd', 'doctorate', 'PhD', 'Doctorate'])
            ->withCount(['phdLearningOutcomes as plo_count'])
            ->orderBy('code')
            ->get();

        return view('admin.programs.index', compact('diplomaPrograms', 'degreePrograms', 'masterPrograms', 'phdPrograms'));
    }

    /**
     * Show the form for creating a new program
     */
    public function create()
    {
        return view('admin.programs.create');
    }

    /**
     * Store a newly created program
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:programs',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|in:certificate,diploma,bachelor,master,phd',
            'duration_months' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);

        Program::create($request->all());

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program created successfully.');
    }

    /**
     * Display the specified program
     */
    public function show(Program $program)
    {
        $program->load(['programLearningOutcomes', 'courseLearningOutcomes', 'programSubjects']);
        return view('admin.programs.show', compact('program'));
    }

    /**
     * Show the form for editing the specified program
     */
    public function edit(Program $program)
    {
        return view('admin.programs.edit', compact('program'));
    }

    /**
     * Update the specified program
     */
    public function update(Request $request, Program $program)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:programs,code,' . $program->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|in:certificate,diploma,bachelor,master,phd',
            'duration_months' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $program->update($request->all());

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program updated successfully.');
    }

    /**
     * Remove the specified program
     */
    public function destroy(Program $program)
    {
        $program->delete();

        return redirect()->route('admin.programs.index')
            ->with('success', 'Program deleted successfully.');
    }

    /**
     * Show PLO management for a program based on academic level
     */
    public function plos(Program $program)
    {
        $plos = $program->getLearningOutcomesByLevel();
        return view('admin.programs.plos', compact('program', 'plos'));
    }

    /**
     * Show Diploma PLO management for a program
     */
    public function diplomaPlos(Program $program)
    {
        $plos = $program->diplomaLearningOutcomes()->ordered()->get();
        return view('admin.programs.diploma-plos', compact('program', 'plos'));
    }

    /**
     * Show Degree PLO management for a program
     */
    public function degreePlos(Program $program)
    {
        $plos = $program->degreeLearningOutcomes()->ordered()->get();
        return view('admin.programs.degree-plos', compact('program', 'plos'));
    }

    /**
     * Show Master PLO management for a program
     */
    public function masterPlos(Program $program)
    {
        $plos = $program->masterLearningOutcomes()->ordered()->get();
        return view('admin.programs.master-plos', compact('program', 'plos'));
    }

    /**
     * Show PhD PLO management for a program
     */
    public function phdPlos(Program $program)
    {
        $plos = $program->phdLearningOutcomes()->ordered()->get();
        return view('admin.programs.phd-plos', compact('program', 'plos'));
    }

    /**
     * Store PLO for a program based on academic level
     */
    public function storePlo(Request $request, Program $program)
    {
        $request->validate([
            'plo_code' => 'required|string|max:10',
            'description' => 'required|string',
            'mqf_domain' => 'required|string|max:50',
            'mqf_code' => 'required|string|max:10',
            'mapped_courses' => 'required|string',
            'sort_order' => 'integer'
        ]);

        // Store PLO based on program level
        switch (strtolower($program->level)) {
            case 'diploma':
                $program->diplomaLearningOutcomes()->create($request->all());
                return redirect()->route('admin.programs.diploma-plos', $program)
                    ->with('success', 'Diploma PLO created successfully.');
            case 'degree':
            case 'bachelor':
                $program->degreeLearningOutcomes()->create($request->all());
                return redirect()->route('admin.programs.degree-plos', $program)
                    ->with('success', 'Degree PLO created successfully.');
            case 'master':
            case 'masters':
                $program->masterLearningOutcomes()->create($request->all());
                return redirect()->route('admin.programs.master-plos', $program)
                    ->with('success', 'Master PLO created successfully.');
            case 'phd':
            case 'doctorate':
                $program->phdLearningOutcomes()->create($request->all());
                return redirect()->route('admin.programs.phd-plos', $program)
                    ->with('success', 'PhD PLO created successfully.');
            default:
                $program->programLearningOutcomes()->create($request->all());
                return redirect()->route('admin.programs.plos', $program)
                    ->with('success', 'PLO created successfully.');
        }
    }

    /**
     * Update PLO
     */
    public function updatePlo(Request $request, ProgramLearningOutcome $plo)
    {
        $request->validate([
            'plo_code' => 'required|string|max:10',
            'description' => 'required|string',
            'mqf_domain' => 'required|string|max:50',
            'mqf_code' => 'required|string|max:10',
            'mapped_courses' => 'required|string',
            'sort_order' => 'integer'
        ]);

        $plo->update($request->all());

        return redirect()->route('admin.programs.plos', $plo->program)
            ->with('success', 'PLO updated successfully.');
    }

    /**
     * Delete PLO
     */
    public function destroyPlo(ProgramLearningOutcome $plo)
    {
        $program = $plo->program;
        $plo->delete();

        return redirect()->route('admin.programs.plos', $program)
            ->with('success', 'PLO deleted successfully.');
    }

    /**
     * Show CLO management for a program
     */
    public function clos(Program $program)
    {
        $clos = $program->courseLearningOutcomes()->ordered()->get();
        return view('admin.programs.clos', compact('program', 'clos'));
    }

    /**
     * Store CLO for a program
     */
    public function storeClo(Request $request, Program $program)
    {
        $request->validate([
            'course_name' => 'required|string|max:100',
            'clo_code' => 'required|string|max:10',
            'description' => 'required|string',
            'mqf_domain' => 'required|string|max:50',
            'mqf_code' => 'required|string|max:10',
            'topics_covered' => 'nullable|string',
            'assessment_methods' => 'nullable|string',
            'sort_order' => 'integer'
        ]);

        $data = $request->all();
        
        // Convert topics_covered and assessment_methods to JSON arrays
        if ($request->topics_covered) {
            $data['topics_covered'] = json_encode(array_filter(array_map('trim', explode("\n", $request->topics_covered))));
        }
        
        if ($request->assessment_methods) {
            $data['assessment_methods'] = json_encode(array_filter(array_map('trim', explode("\n", $request->assessment_methods))));
        }
        
        $program->courseLearningOutcomes()->create($data);

        return redirect()->route('admin.programs.clos', $program)
            ->with('success', 'CLO created successfully.');
    }

    /**
     * Delete CLO for a program
     */
    public function destroyClo(CourseLearningOutcome $clo)
    {
        $program = $clo->program;
        $clo->delete();
        
        return redirect()->route('admin.programs.clos', $program)
            ->with('success', 'CLO deleted successfully.');
    }

    /**
     * Show subjects management for a program
     */
    public function subjects(Program $program)
    {
        $subjects = $program->programSubjects()->ordered()->get();
        return view('admin.programs.subjects', compact('program', 'subjects'));
    }

    /**
     * Store subject for a program
     */
    public function storeSubject(Request $request, Program $program)
    {
        $request->validate([
            'subject_name' => 'required|string|max:200',
            'subject_code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'classification' => 'required|string|max:50',
            'credit_hours' => 'required|integer|min:1',
            'teaching_hours' => 'nullable|string',
            'assessment_methods' => 'nullable|string',
            'sort_order' => 'integer'
        ]);

        $program->programSubjects()->create($request->all());

        return redirect()->route('admin.programs.subjects', $program)
            ->with('success', 'Subject created successfully.');
    }

    /**
     * Extract PLOs/CLOs from Word document
     */
    public function extractFromDocument(Request $request, Program $program)
    {
        $request->validate([
            'document' => 'required|file|mimes:docx,doc,pdf|max:10240'
        ]);

        $file = $request->file('document');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('temp', $filename);

        try {
            if ($file->getClientOriginalExtension() === 'docx') {
                $this->extractFromWord($program, storage_path('app/' . $path));
            } elseif ($file->getClientOriginalExtension() === 'pdf') {
                $this->extractFromPdf($program, storage_path('app/' . $path));
            }

            Storage::delete($path);

            return redirect()->route('admin.programs.show', $program)
                ->with('success', 'PLOs/CLOs extracted and imported successfully.');
        } catch (\Exception $e) {
            Storage::delete($path);
            return redirect()->back()
                ->with('error', 'Error extracting document: ' . $e->getMessage());
        }
    }

    /**
     * Extract from Word document
     */
    private function extractFromWord(Program $program, $filePath)
    {
        // This is a basic implementation
        // You would need to implement more sophisticated parsing
        // based on your document structure
        
        $phpWord = IOFactory::load($filePath);
        $sections = $phpWord->getSections();
        
        foreach ($sections as $section) {
            $elements = $section->getElements();
            foreach ($elements as $element) {
                if (get_class($element) === 'PhpOffice\PhpWord\Element\TextRun') {
                    $text = $element->getText();
                    
                    // Look for PLO patterns
                    if (preg_match('/PLO(\d+):\s*(.+)/', $text, $matches)) {
                        $this->createPloFromText($program, $matches[1], $matches[2]);
                    }
                    
                    // Look for CLO patterns
                    if (preg_match('/CLO(\d+):\s*(.+)/', $text, $matches)) {
                        $this->createCloFromText($program, $matches[1], $matches[2]);
                    }
                }
            }
        }
    }

    /**
     * Extract from PDF document
     */
    private function extractFromPdf(Program $program, $filePath)
    {
        // Implement PDF text extraction
        // You might want to use a library like Smalot\PdfParser
        // This is a placeholder implementation
    }

    /**
     * Create PLO from extracted text
     */
    private function createPloFromText(Program $program, $ploNumber, $description)
    {
        $program->programLearningOutcomes()->create([
            'plo_code' => 'PLO' . $ploNumber,
            'description' => trim($description),
            'mqf_domain' => 'To be determined',
            'mqf_code' => 'TBD',
            'mapped_courses' => 'To be determined',
            'sort_order' => (int)$ploNumber
        ]);
    }

    /**
     * Create CLO from extracted text
     */
    private function createCloFromText(Program $program, $cloNumber, $description)
    {
        $program->courseLearningOutcomes()->create([
            'course_name' => 'Course to be determined',
            'clo_code' => 'CLO' . $cloNumber,
            'description' => trim($description),
            'mqf_domain' => 'To be determined',
            'mqf_code' => 'TBD',
            'topics_covered' => 'To be determined',
            'assessment_methods' => 'To be determined',
            'sort_order' => (int)$cloNumber
        ]);
    }
}