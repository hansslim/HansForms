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

    public function form() {
        return $this->belongsTo(Form::class);
    }

    public function inputElements(){
        return $this->hasMany(InputElement::class);
    }

    public function newPages(){
        return $this->hasMany(NewPage::class);
    }
}
