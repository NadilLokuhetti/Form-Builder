<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Field extends Model
{
    use HasFactory;

    protected $fillable = ['form_id', 'type', 'label', 'required', 'options', 'order'];

    protected $casts = [
        'required' => 'boolean',
        'options' => 'array'
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}