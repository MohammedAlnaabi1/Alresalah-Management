<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    use HasFactory;

    protected $table = 'revenues';

    // ✅ الحقول المسموح تعبئتها
    protected $fillable = [
        'source',      // مصدر الإيراد
        'amount',      // المبلغ
        'type',        // نوع الإيراد
        'date',        // التاريخ
        'notes',       // الملاحظات
        'attachment',  // المرفق (فاتورة)
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
