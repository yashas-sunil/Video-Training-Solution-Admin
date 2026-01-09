<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLessonIdToChapterManualContentsTable extends Migration
{
    public function up()
    {
        Schema::table('chapter_manual_contents', function (Blueprint $table) {
            if (!Schema::hasColumn('chapter_manual_contents', 'lesson_id')) {
                $table->unsignedBigInteger('lesson_id')->nullable()->after('chapter_id');
                $table->index('lesson_id');
            }
        });
    }

    public function down()
    {
        Schema::table('chapter_manual_contents', function (Blueprint $table) {
            $table->dropIndex(['lesson_id']);
            $table->dropColumn('lesson_id');
        });
    }
}
