<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChaptersIdToTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('topics', function (Blueprint $table) {
               if (!Schema::hasColumn('topics', 'course_id')) {
                $table->unsignedBigInteger('course_id')->nullable()->after('id');
            }

            if (!Schema::hasColumn('topics', 'level_id')) {
                $table->unsignedBigInteger('level_id')->nullable()->after('course_id');
            }

            if (!Schema::hasColumn('topics', 'subject_id')) {
                $table->unsignedBigInteger('subject_id')->nullable()->after('level_id');
            }

            if (!Schema::hasColumn('topics', 'chapters_id')) {
                $table->unsignedBigInteger('chapters_id')->nullable()->after('subject_id');
            }

            if (!Schema::hasColumn('topics', 'subchapter_id')) {
                $table->unsignedBigInteger('subchapter_id')->nullable()->after('chapters_id');
            }

            if (!Schema::hasColumn('topics', 'name')) {
                $table->string('name', 255)->nullable()->after('subchapter_id');
            }

            if (!Schema::hasColumn('topics', 'status')) {
                $table->tinyInteger('status')->default(1)->after('name');
            }

            if (!Schema::hasColumn('topics', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('status');
            }

            if (!Schema::hasColumn('topics', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }

            // Indexes
            $table->index(['course_id', 'level_id', 'subject_id', 'chapters_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('topics', function (Blueprint $table) {
               $columns = [
                'course_id',
                'level_id',
                'subject_id',
                'chapters_id',
                'subchapter_id',
                'name',
                'status',
                'created_by',
                'updated_by',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('topics', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
