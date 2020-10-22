<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
    ];

    protected $casts = [
        'user_id' => 'integer'
    ];

    public function entries()
    {
        return $this->hasMany(ContactEntry::class);
    }
}
