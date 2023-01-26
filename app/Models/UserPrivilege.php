<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPrivilege extends Model
{
    use HasFactory;

    protected $table = 'cms_privileges';

    public function scopeWithName($query,$name)
    {
        return $query->where('name',$name)->first();
    }
}
