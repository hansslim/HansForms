<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BooleanInput extends Model
{
    use HasFactory;

    protected $fillable = [
        'input_element_id'
    ];

    protected $visible = [
        'id',
        'input_element_id',
        'booleanInputAnswers'
    ];

    public function inputElement() {
        return $this->belongsTo(InputElement::class);
    }

    public function booleanInputAnswers() {
        return $this->hasMany(BooleanInputAnswer::class);
    }
}
