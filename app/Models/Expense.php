<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'expenses'; // اسم الجدول

    protected $fillable = [
    'category', 'payment_method', 'amount', 'date',
    'related_bus_id', 'notes', 'status', 'attachment'
];


    // 🔹 ربط المصروف بالحافلة (علاقة اختيارية)
    public function bus()
    {
        return $this->belongsTo(Bus::class, 'related_bus_id');
    }

    protected $casts = [
        'date' => 'date',
    ];
}
