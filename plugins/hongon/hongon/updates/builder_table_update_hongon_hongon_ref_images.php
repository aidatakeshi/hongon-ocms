<?php namespace Hongon\Hongon\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHongonHongonRefImages extends Migration
{
    public function up()
    {
        Schema::table('hongon_hongon_ref_images', function($table)
        {
            $table->text('name')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('hongon_hongon_ref_images', function($table)
        {
            $table->text('name')->nullable(false)->change();
        });
    }
}
