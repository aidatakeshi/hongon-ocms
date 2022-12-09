<?php namespace Hongon\Hongon\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHongonHongonRefImages2 extends Migration
{
    public function up()
    {
        Schema::table('hongon_hongon_ref_images', function($table)
        {
            $table->double('x_min', 10, 0)->nullable()->change();
            $table->double('x_max', 10, 0)->nullable()->change();
            $table->double('y_min', 10, 0)->nullable()->change();
            $table->double('y_max', 10, 0)->nullable()->change();
            $table->string('file_url', 255)->nullable()->change();
            $table->double('hide_below_logzoom', 10, 0)->nullable()->change();
            $table->text('name')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('hongon_hongon_ref_images', function($table)
        {
            $table->double('x_min', 10, 0)->nullable(false)->change();
            $table->double('x_max', 10, 0)->nullable(false)->change();
            $table->double('y_min', 10, 0)->nullable(false)->change();
            $table->double('y_max', 10, 0)->nullable(false)->change();
            $table->string('file_url', 255)->nullable(false)->change();
            $table->double('hide_below_logzoom', 10, 0)->nullable(false)->change();
            $table->text('name')->nullable(false)->change();
        });
    }
}
