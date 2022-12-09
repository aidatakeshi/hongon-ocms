<?php namespace Hongon\Hongon\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHongonHongonLineSections extends Migration
{
    public function up()
    {
        Schema::create('hongon_hongon_line_sections', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id');
            $table->string('line_id')->nullable();
            $table->string('operator_id')->nullable();
            $table->text('name_chi')->nullable();
            $table->text('name_eng')->nullable();
            $table->text('name_short_chi')->nullable();
            $table->text('name_short_eng')->nullable();
            $table->string('color')->nullable();
            $table->string('color_text')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('max_speed_kph')->nullable();
            $table->json('stations')->default('[]');
            $table->json('_data')->default('{}');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->primary(['id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('hongon_hongon_line_sections');
    }
}
