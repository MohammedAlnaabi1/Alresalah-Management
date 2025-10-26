<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_number',
        'bus_type',
        'driver_name',
        'bus_code',
        'school',
        'status',
        'ownership_pdf',
        'insurance_pdf',
    ];

    // ✅ علاقة الحافلة مع المصروفات
    public function expenses()
    {
        return $this->hasMany(BusExpense::class, 'bus_id');
    }
}
