<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NumberInputAnswer extends Model
{
    use HasFactory;

    protected $table = "number_inputs_answers";

    protected $fillable = [
        'value',
        'form_completion_id',
        'number_input_id'
    ];

    public function formCompletion()
    {
        return $this->belongsTo(FormCompletion::class);
    }

    public function numberInput()
    {
        return $this->belongsTo(NumberInput::class);
    }
}
