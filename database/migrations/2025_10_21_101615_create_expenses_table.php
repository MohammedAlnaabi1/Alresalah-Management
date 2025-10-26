<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // نوع المصروف
            $table->decimal('amount', 10, 2); // المبلغ
            $table->string('payment_method'); // طريقة الدفع
            $table->date('date'); // التاريخ
            $table->text('notes')->nullable(); // الملاحظات
            $table->unsignedBigInteger('related_bus_id')->nullable(); // رقم الحافلة (اختياري)
            $table->string('attachment')->nullable(); // المرفق
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
