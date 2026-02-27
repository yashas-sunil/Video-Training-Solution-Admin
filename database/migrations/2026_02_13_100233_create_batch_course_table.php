<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatchCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_course', function (Blueprint $table) {
            $table->id();
                $table->unsignedBigInteger('batch_id');
    $table->unsignedBigInteger('scorm_package_id');

    $table->foreign('batch_id')->references('id')->on('batches')->onDelete('cascade');
    $table->foreign('scorm_package_id')->references('id')->on('scorm_packages')->onDelete('cascade');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('batch_course');
    }
}
