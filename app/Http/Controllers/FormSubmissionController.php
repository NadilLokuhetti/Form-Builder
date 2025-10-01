<?php
// app/Http/Controllers/FormSubmissionController.php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Http\Request;

class FormSubmissionController extends Controller
{
    public function index(Form $form)
    {
        $submissions = $form->submissions()->with('answers.field')->latest()->get();
        return response()->json($submissions);
    }

    public function store(Request $request, Form $form)
    {
        $validationRules = [];
        foreach ($form->fields as $field) {
            if ($field->required) {
                $validationRules["field_{$field->id}"] = 'required';
            }
        }

        $request->validate($validationRules);

        $submission = $form->submissions()->create();

        foreach ($form->fields as $field) {
            $fieldValue = $request->input("field_{$field->id}");
            
            if ($fieldValue) {
                $submission->answers()->create([
                    'field_id' => $field->id,
                    'value' => is_array($fieldValue) ? json_encode($fieldValue) : $fieldValue
                ]);
            }
        }

        return response()->json(['message' => 'Form submitted successfully']);
    }

    public function show(FormSubmission $submission)
    {
        return response()->json($submission->load('answers.field'));
    }
}