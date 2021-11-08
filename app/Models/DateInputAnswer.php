<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DateInputAnswer extends Model
{
    use HasFactory;

    protected $table = "date_inputs_answers";

    protected $fillable = [
        'value',
        'form_completion_id',
        'date_input_id'
    ];

    public function formCompletion() {
        return $this->belongsTo(FormCompletion::class);
    }

    public function dateInput() {
        return $this->belongsTo(DateInput::class);
    }
}
