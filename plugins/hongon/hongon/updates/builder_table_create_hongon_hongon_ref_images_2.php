<?php namespace Hongon\Hongon\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHongonHongonRefImages2 extends Migration
{
    public function up()
    {
        Schema::create('hongon_hongon_ref_images', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id');
            $table->double('x_min', 10, 0);
            $table->double('x_max', 10, 0);
            $table->double('y_min', 10, 0);
            $table->double('y_max', 10, 0);
            $table->string('file_url');
            $table->double('hide_below_logzoom', 10, 0);
            $table->text('name');
            $table->bigInteger('sort')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->primary(['id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('hongon_hongon_ref_images');
    }
}