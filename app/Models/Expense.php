<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'expenses';

    protected $fillable = [
        'category',
        'payment_method',
        'amount',
        'date',
        'related_bus_id',
        'bus_expense_id', // ✅ أضف هذا الحقل
        'notes',
        'status',
        'attachment',
    ];

    // 🔹 ربط المصروف بالحافلة (في حال وجود رقم حافلة)
    public function bus()
    {
        return $this->belongsTo(Bus::class, 'related_bus_id');
    }

    // 🔹 ربط المصروف المالي بمصروف الحافلة الأصلي
    public function busExpense()
    {
        return $this->belongsTo(BusExpense::class, 'bus_expense_id');
    }

    protected $casts = [
        'date' => 'date',
    ];
}
