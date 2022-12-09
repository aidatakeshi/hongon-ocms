<?php namespace Hongon\Hongon\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHongonHongonLines extends Migration
{
    public function up()
    {
        Schema::rename('hongon_hongon_rail_lines', 'hongon_hongon_lines');
    }
    
    public function down()
    {
        Schema::rename('hongon_hongon_lines', 'hongon_hongon_rail_lines');
    }
}
