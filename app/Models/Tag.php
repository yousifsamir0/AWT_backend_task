<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name'];

    protected $hidden = ['created_at', 'updated_at'];



    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function posts()
    {

        return $this->belongsToMany(Post::class);
    }
}
