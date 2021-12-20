<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TextInput extends Model
{
    use HasFactory;

    protected $fillable = [
        'input_element_id',
        'min_length',
        'max_length',
        'strict_length'
    ];

    protected $visible = [
        'id',
        'input_element_id',
        'min_length',
        'max_length',
        'strict_length',
        'textInputAnswers'
    ];

    public function inputElement() {
        return $this->belongsTo(InputElement::class);
    }

    public function textInputAnswers() {
        return $this->hasMany(TextInputAnswer::class);
    }
}
