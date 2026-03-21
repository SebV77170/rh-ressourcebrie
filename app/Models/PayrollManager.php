<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollManager extends Model
{
    use HasFactory;

    protected $table = 'payroll_manager';

    protected $fillable = [
        'uuid_user',
    ];
}
