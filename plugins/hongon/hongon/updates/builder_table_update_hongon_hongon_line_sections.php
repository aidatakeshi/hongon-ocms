<?php namespace Hongon\Hongon\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHongonHongonLineSections extends Migration
{
    public function up()
    {
        Schema::table('hongon_hongon_line_sections', function($table)
        {
            $table->bigInteger('sort_in_line')->default(0);
        });
    }
    
    public function down()
    {
        Schema::table('hongon_hongon_line_sections', function($table)
        {
            $table->dropColumn('sort_in_line');
        });
    }
}
