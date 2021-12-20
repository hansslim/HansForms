<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BooleanInputAnswer extends Model
{
    use HasFactory;

    protected $table = "boolean_inputs_answers";

    protected $fillable = [
        'value',
        'form_completion_id',
        'boolean_input_id'
    ];

    public function formCompletion()
    {
        return $this->belongsTo(FormCompletion::class);
    }

    public function booleanInput()
    {
        return $this->belongsTo(BooleanInput::class);
    }
}
