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
        'inputElements',
        'newPages'
    ];

    protected $with = ['inputElements', 'newPages'];

    public function form() {
        return $this->belongsTo(Form::class);
    }

    public function inputElements(){
        return $this->hasOne(InputElement::class);
    }

    public function newPages(){
        return $this->hasOne(NewPage::class);
    }
}
