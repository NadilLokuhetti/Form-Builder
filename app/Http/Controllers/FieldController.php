<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Form;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function store(Request $request, Form $form)
    {
        $request->validate([
            'type' => 'required|in:text,textarea,checkbox,radio',
            'label' => 'required|string|max:255',
            'required' => 'boolean',
            'options' => 'nullable|array',
            'order' => 'integer'
        ]);

        $field = $form->fields()->create($request->all());

        return response()->json($field, 201);
    }

    public function update(Request $request, Field $field)
    {
        $request->validate([
            'type' => 'in:text,textarea,checkbox,radio',
            'label' => 'string|max:255',
            'required' => 'boolean',
            'options' => 'nullable|array',
            'order' => 'integer'
        ]);

        $field->update($request->all());

        return response()->json($field);
    }

    public function destroy(Field $field)
    {
        $field->delete();
        return response()->json(null, 204);
    }

    public function reorder(Request $request, Form $form)
    {
        $request->validate([
            'fields' => 'required|array',
            'fields.*.id' => 'required|exists:fields,id',
            'fields.*.order' => 'required|integer'
        ]);

        foreach ($request->fields as $fieldData) {
            Field::where('id', $fieldData['id'])
                ->update(['order' => $fieldData['order']]);
        }

        return response()->json(['message' => 'Fields reordered successfully']);
    }
}