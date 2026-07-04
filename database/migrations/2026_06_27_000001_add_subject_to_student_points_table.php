<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('student_points', function (Blueprint $table) {
            $table->string('subject')->after('student_id')->default('عام');
        });
    }

    public function down()
    {
        Schema::table('student_points', function (Blueprint $table) {
            $table->dropColumn('subject');
        });
    }
};
