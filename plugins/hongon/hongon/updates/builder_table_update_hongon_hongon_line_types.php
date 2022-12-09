<?php namespace Hongon\Hongon\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHongonHongonLineTypes extends Migration
{
    public function up()
    {
        Schema::table('hongon_hongon_line_types', function($table)
        {
            $table->text('name_chi')->nullable()->change();
            $table->text('name_eng')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('hongon_hongon_line_types', function($table)
        {
            $table->text('name_chi')->nullable(false)->change();
            $table->text('name_eng')->nullable(false)->change();
        });
    }
}
