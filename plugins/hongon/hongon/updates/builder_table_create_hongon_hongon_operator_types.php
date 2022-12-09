<?php namespace Hongon\Hongon\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHongonHongonOperatorTypes extends Migration
{
    public function up()
    {
        Schema::create('hongon_hongon_operator_types', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id');
            $table->text('name_chi');
            $table->text('name_eng');
            $table->text('remarks')->nullable();
            $table->bigInteger('sort')->default(0);
            $table->string('map_color')->nullable();
            $table->integer('map_thickness')->nullable();
            $table->decimal('hide_below_logzoom', 10, 0)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->primary(['id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('hongon_hongon_operator_types');
    }
}