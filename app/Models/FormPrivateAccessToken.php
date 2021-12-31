<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormPrivateAccessToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'was_used',
        'token',
        'email',
        'form_id',
    ];

    protected $visible = [
        'id',
        'is_used',
        'token',
        'email',
        'form_id',
    ];

    public function form() {
        return $this->belongsTo(Form::class);
    }
}
