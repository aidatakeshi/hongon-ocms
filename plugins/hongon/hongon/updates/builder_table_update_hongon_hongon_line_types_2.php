<?php namespace Hongon\Hongon\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHongonHongonLineTypes2 extends Migration
{
    public function up()
    {
        Schema::table('hongon_hongon_line_types', function($table)
        {
            $table->double('hide_below_logzoom', 10, 0)->nullable()->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('hongon_hongon_line_types', function($table)
        {
            $table->double('hide_below_logzoom', 10, 0)->nullable(false)->default(0)->change();
        });
    }
}
