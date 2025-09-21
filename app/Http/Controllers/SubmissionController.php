<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Submission;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index(Form $form)
    {
        $submissions = $form->submissions()->latest()->get();
        return response()->json($submissions);
    }

    public function store(Request $request, Form $form)
    {
        $request->validate([
            'data' => 'required|array'
        ]);

        $submission = $form->submissions()->create([
            'data' => $request->data,
            'ip_address' => $request->ip()
        ]);

        return response()->json($submission, 201);
    }

    public function show(Submission $submission)
    {
        return response()->json($submission);
    }
}