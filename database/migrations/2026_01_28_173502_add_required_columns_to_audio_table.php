<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRequiredColumnsToAudioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audio', function (Blueprint $table) {
             if (!Schema::hasColumn('audio', 'topic_id')) {
                $table->unsignedBigInteger('topic_id')->nullable()->after('id');
            }

            if (!Schema::hasColumn('audio', 'name')) {
                $table->string('name', 255)->nullable()->after('topic_id');
            }

            if (!Schema::hasColumn('audio', 'flash_card_name')) {
                $table->string('flash_card_name', 255)->nullable()->after('name');
            }

            if (!Schema::hasColumn('audio', 'audio_content')) {
                $table->longText('audio_content')->nullable()->after('flash_card_name');
            }

            if (!Schema::hasColumn('audio', 'error_message')) {
                $table->string('error_message', 255)->nullable()->after('audio_content');
            }

            if (!Schema::hasColumn('audio', 'status')) {
                $table->tinyInteger('status')->default(1)->after('error_message');
            }

            // Index
            $table->index('topic_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audio', function (Blueprint $table) {
               $columns = [
                'topic_id',
                'name',
                'flash_card_name',
                'audio_content',
                'error_message',
                'status',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('audio', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
