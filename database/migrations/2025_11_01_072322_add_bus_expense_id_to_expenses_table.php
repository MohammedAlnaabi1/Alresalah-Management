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
    public function up()
{
    Schema::table('expenses', function (Blueprint $table) {
        $table->unsignedBigInteger('bus_expense_id')->nullable()->after('related_bus_id');
        $table->foreign('bus_expense_id')->references('id')->on('bus_expenses')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('expenses', function (Blueprint $table) {
        $table->dropForeign(['bus_expense_id']);
        $table->dropColumn('bus_expense_id');
    });
}
};