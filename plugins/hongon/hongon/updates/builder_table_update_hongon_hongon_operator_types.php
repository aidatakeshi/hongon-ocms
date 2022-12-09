<?php namespace Hongon\Hongon\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateHongonHongonOperatorTypes extends Migration
{
    public function up()
    {
        Schema::table('hongon_hongon_operator_types', function($table)
        {
            $table->double('hide_below_logzoom', 10, 0)->nullable()->unsigned(false)->default(null)->comment(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('hongon_hongon_operator_types', function($table)
        {
            $table->decimal('hide_below_logzoom', 10, 0)->nullable()->unsigned(false)->default(null)->comment(null)->change();
        });
    }
}
