<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'email', 'department_id', 'salary'];

    protected $hidden = ['deleted_at'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

}
