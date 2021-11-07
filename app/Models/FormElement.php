<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormElement extends Model
{
    use HasFactory;

    protected $fillable = [
        'order',
        'form_id'
    ];

    protected $visible = [
        'order',
        'inputElement',
        'newPage'
    ];

    protected $with = ['inputElement', 'newPage'];

    public function form() {
        return $this->belongsTo(Form::class);
    }

    public function inputElement(){
        return $this->hasOne(InputElement::class);
    }

    public function newPage(){
        return $this->hasOne(NewPage::class);
    }
}
