<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDifficultLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('difficult_levels', function (Blueprint $table) {
            $table->id();
               $table->string('value');               
        $table->string('name');                
        $table->integer('correct_marks');      
        $table->integer('incorrect_marks');    

        $table->tinyInteger('status')->default(1); 
        
        $table->unsignedBigInteger('created_by')->nullable();
        $table->unsignedBigInteger('updated_by')->nullable();
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
        Schema::dropIfExists('difficult_levels');
    }
}
