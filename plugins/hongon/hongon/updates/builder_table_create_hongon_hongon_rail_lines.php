<?php namespace Hongon\Hongon\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHongonHongonRailLines extends Migration
{
    public function up()
    {
        Schema::create('hongon_hongon_rail_lines', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id');
            $table->string('line_type_id')->nullable();
            $table->text('name_chi');
            $table->text('name_eng');
            $table->text('name_short_chi')->nullable();
            $table->text('name_short_eng')->nullable();
            $table->text('remarks')->nullable();
            $table->json('_data')->default('{}');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->primary(['id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('hongon_hongon_rail_lines');
    }
}
