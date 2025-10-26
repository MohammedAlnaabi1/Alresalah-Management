<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->string('bus_number');     // لوحة الحافلة
            $table->string('bus_type');       // نوع الحافلة (هايس / كوستر)
            $table->string('driver_name');    // اسم السائق
            $table->string('bus_code');       // رقم الحافلة الداخلي
            $table->string('school');         // المدرسة التابعة لها
            $table->string('status');         // الحالة (نشطة / متوقفة / صيانة)
            $table->string('ownership_pdf')->nullable(); // ملف الملكية
            $table->string('insurance_pdf')->nullable(); // ملف التأمين
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};
