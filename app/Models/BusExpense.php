<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_id',
        'expense_type',
        'description',
        'amount',
        'expense_date',
        'receipt_pdf',
    ];

    // ✅ علاقة المصروف مع الحافلة
    public function bus()
    {
        return $this->belongsTo(Bus::class, 'bus_id');
    }
}
