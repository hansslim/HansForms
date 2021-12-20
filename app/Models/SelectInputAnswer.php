<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectInputAnswer extends Model
{
    use HasFactory;

    protected $table = "select_inputs_answers";

    protected $fillable = [
        'value',
        'form_completion_id',
        'select_input_id',
        'select_choice_id'
    ];

    public function formCompletion()
    {
        return $this->belongsTo(FormCompletion::class);
    }

    public function selectInput()
    {
        return $this->belongsTo(SelectInput::class);
    }

    public function selectInputChoice()
    {
        return $this->belongsTo(SelectInputChoice::class);
    }
}
