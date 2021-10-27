<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InputElement extends Model
{
    use HasFactory;

    protected $fillable = [
        'header',
        'description',
        'is_mandatory',
        'form_element_id'
    ];

    protected $visible = [
        'header',
        'description',
        'is_mandatory',
        'booleanInput',
        'dateInput',
        'textInput',
        'numberInput',
        'selectInput'
    ];

    protected $with = [
        'booleanInput',
        'dateInput',
        'textInput',
        'numberInput',
        'selectInput'
    ];

    public function formElement() {
        return $this->belongsTo(FormElement::class);
    }

    public function booleanInput() {
        return $this->hasOne(BooleanInput::class);
    }

    public function dateInput() {
        return $this->hasOne(DateInput::class);
    }

    public function textInput() {
        return $this->hasOne(TextInput::class);
    }

    public function numberInput() {
        return $this->hasOne(NumberInput::class);
    }

    public function selectInput() {
        return $this->hasOne(SelectInput::class);
    }
}
