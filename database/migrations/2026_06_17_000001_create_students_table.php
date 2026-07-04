<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('grade');
            $table->string('class_name');
            $table->date('date_of_birth');
            $table->enum('gender', ['ذكر', 'أنثى']);
            $table->date('enrollment_date');
            $table->string('parent_name');
            $table->string('phone');
            $table->text('address')->nullable();
            $table->string('status')->default('نشط');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
};
