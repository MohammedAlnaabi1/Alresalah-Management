<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bus_expenses', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('pending')
                  ->after('receipt_pdf');
        });
    }

    public function down()
    {
        Schema::table('bus_expenses', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
