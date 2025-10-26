<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
    Schema::create('bus_expenses', function (Blueprint $table) {
        $table->id();
        $table->foreignId('bus_id')->constrained('buses')->onDelete('cascade'); // ربط بالحافلة
        $table->string('expense_type');   // نوع المصروف (وقود / صيانة / غسيل / ... )
        $table->text('description')->nullable(); // وصف إضافي
        $table->decimal('amount', 10, 2); // المبلغ بالريال
        $table->date('expense_date');     // تاريخ المصروف
        $table->string('receipt_pdf')->nullable(); // ملف مرفق (فاتورة مثلاً)
        $table->timestamps();
    });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bus_expenses');
    }
};
