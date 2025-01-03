<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcelImport extends Model
{
    use HasFactory;

    protected $table = 'excel_imports';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
    ];

    public $timestamps = true;

}
