<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'end_time',
        'start_time',
        'has_private_token',
        'user_id'
    ];

    protected $visible = [
        'slug',
        'name',
        'description',
        'start_time',
        'end_time',
        'formElements',
        'user',
        'is_expired'
    ];

    protected $with = ['formElements'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formElements()
    {
        return $this->hasMany(FormElement::class);
    }

    public function formCompletions()
    {
        return $this->hasMany(FormCompletion::class);
    }
}
