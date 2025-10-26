<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revenues', function (Blueprint $table) {
            $table->id();
            $table->string('source'); // مصدر الإيراد
            $table->decimal('amount', 10, 2); // المبلغ
            $table->string('type'); // نوع الإيراد (نقل - تبرع - دعم...)
            $table->date('date'); // التاريخ
            $table->text('notes')->nullable(); // ملاحظات
            $table->string('attachment')->nullable(); // المرفق (فاتورة أو إيصال)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revenues');
    }
};
