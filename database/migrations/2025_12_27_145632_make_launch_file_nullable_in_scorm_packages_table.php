<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeLaunchFileNullableInScormPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scorm_packages', function (Blueprint $table) {
              $table->string('launch_file')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scorm_packages', function (Blueprint $table) {
                    $table->string('launch_file')->nullable(false)->change();

        });
    }
}
