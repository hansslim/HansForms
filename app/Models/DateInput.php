<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DateInput extends Model
{
    use HasFactory;

    protected $fillable = [
        'input_element_id',
        'min',
        'max'
    ];

    protected $visible = [
        'id',
        'input_element_id',
        'min',
        'max',
        'dateInputAnswers'
    ];

    public function inputElement() {
        return $this->belongsTo(InputElement::class);
    }

    public function dateInputAnswers() {
        return $this->hasMany(DateInputAnswer::class);
    }
}
