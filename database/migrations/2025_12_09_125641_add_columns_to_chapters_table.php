<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToChaptersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chapters', function (Blueprint $table) {
             $table->integer('chapter_order')->nullable()->after('id');
        $table->unsignedBigInteger('chap_imp_id')->nullable()->after('chapter_order');
        $table->integer('sat_total_time')->nullable()->after('chap_imp_id');

        $table->tinyInteger('status')->default(1)->after('sat_total_time');

        $table->unsignedBigInteger('created_by')->nullable()->after('status');
        $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chapters', function (Blueprint $table) {
              $table->dropColumn([
            'chapter_order',
            'chap_imp_id',
            'sat_total_time',
            'status',
            'created_by',
            'updated_by'
        ]);
        });
    }
}
