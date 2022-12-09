<?php namespace Hongon\Hongon\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableDeleteHongonHongonRefImages extends Migration
{
    public function up()
    {
        Schema::dropIfExists('hongon_hongon_ref_images');
    }
    
    public function down()
    {
        Schema::create('hongon_hongon_ref_images', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id', 255);
            $table->double('x_min', 10, 0);
            $table->double('x_max', 10, 0);
            $table->double('y_min', 10, 0);
            $table->double('y_max', 10, 0);
            $table->string('file_url', 255);
            $table->double('hide_below_logzoom', 10, 0);
            $table->text('name')->nullable();
            $table->bigInteger('sort')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->primary(['id','x_min','x_max','y_min','y_max','file_url','hide_below_logzoom']);
        });
    }
}
