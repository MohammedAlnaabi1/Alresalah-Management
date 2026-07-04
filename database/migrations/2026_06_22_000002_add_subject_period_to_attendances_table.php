<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Drop foreign key first (name may vary)
        Schema::table('attendances', function (Blueprint $table) {
            $sm = DB::getDoctrineSchemaManager();
            $foreignKeys = $sm->listTableForeignKeys('attendances');
            foreach ($foreignKeys as $fk) {
                if (in_array('student_id', $fk->getLocalColumns())) {
                    $table->dropForeign($fk->getName());
                }
            }
        });

        // Drop unique index
        Schema::table('attendances', function (Blueprint $table) {
            try {
                $table->dropUnique(['student_id', 'date']);
            } catch (\Exception $e) {
                // Index may not exist
            }
        });

        // Add columns and re-create constraints
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'subject')) {
                $table->string('subject')->after('date')->default('الفقه');
            }
            if (!Schema::hasColumn('attendances', 'period')) {
                $table->unsignedTinyInteger('period')->after('subject')->default(1);
            }
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->unique(['student_id', 'date', 'subject', 'period']);
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropUnique(['student_id', 'date', 'subject', 'period']);
            $table->dropColumn(['subject', 'period']);
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->unique(['student_id', 'date']);
        });
    }
};
