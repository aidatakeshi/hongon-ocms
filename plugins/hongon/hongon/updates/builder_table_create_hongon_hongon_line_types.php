<?php namespace Hongon\Hongon\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHongonHongonLineTypes extends Migration
{
    public function up()
    {
        Schema::create('hongon_hongon_line_types', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id');
            $table->text('name_chi');
            $table->text('name_eng');
            $table->text('remarks')->nullable();
            $table->string('map_color')->nullable();
            $table->integer('map_thickness')->nullable();
            $table->double('hide_below_logzoom', 10, 0)->default(0);
            $table->bigInteger('sort')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->primary(['id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('hongon_hongon_line_types');
    }
}
