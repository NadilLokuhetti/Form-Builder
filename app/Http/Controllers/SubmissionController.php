<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Submission;
use App\Models\Answer;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    // GET /api/forms/{form}/submissions
    public function index(Form $form)
    {
        return $form->submissions()->with('answers.field')->latest()->get();
    }

    // GET /api/submissions/{submission}
    public function show(Submission $submission)
    {
        return $submission->load('answers.field');
    }

    // POST /api/forms/{form}/submit
    public function store(Request $request, Form $form)
    {
        $data = $request->validate([
            'answers' => 'required|array',
        ]);

        $submission = $form->submissions()->create([
            'ip_address' => $request->ip(),
        ]);

        foreach ($data['answers'] as $fieldId => $value) {
            Answer::create([
                'submission_id' => $submission->id,
                'field_id' => $fieldId,
                'value' => is_array($value) ? json_encode($value) : $value,
            ]);
        }

        return response()->json(['message' => 'Submission saved successfully'], 201);
    }
}
