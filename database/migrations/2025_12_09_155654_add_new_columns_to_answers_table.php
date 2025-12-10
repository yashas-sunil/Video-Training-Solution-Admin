<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('answers', function (Blueprint $table) {
             $table->unsignedBigInteger('answer_types_id')->nullable()->after('id');
        $table->string('correctans')->nullable()->after('answer_types_id');
        $table->string('option_image')->nullable()->after('correctans');
        $table->boolean('image_flag')->default(0)->after('option_image');
        $table->boolean('status')->default(1)->after('image_flag');
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
        Schema::table('answers', function (Blueprint $table) {
              $table->dropColumn([
            'answer_types_id',
            'correctans',
            'option_image',
            'image_flag',
            'status',
            'created_by',
            'updated_by'
        ]);
        });
    }
}
