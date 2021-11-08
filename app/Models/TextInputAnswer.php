<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TextInputAnswer extends Model
{
    use HasFactory;

    protected $table = "text_inputs_answers";

    protected $fillable = [
        'value',
        'form_completion_id',
        'text_input_id'
    ];

    public function formCompletion() {
        return $this->belongsTo(FormCompletion::class);
    }

    public function textInput() {
        return $this->belongsTo(TextInput::class);
    }
}
