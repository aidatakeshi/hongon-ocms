<?php namespace Hongon\Hongon\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHongonHongonStations extends Migration
{
    public function up()
    {
        Schema::create('hongon_hongon_stations', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id');
            $table->string('major_operator_id')->nullable();
            $table->string('region_id')->nullable();
            $table->text('name_chi');
            $table->text('name_eng');
            $table->text('name_short_chi')->nullable();
            $table->text('name_short_eng')->nullable();
            $table->double('x', 10, 0)->nullable();
            $table->double('y', 10, 0)->nullable();
            $table->double('altitude_m', 10, 0)->nullable();
            $table->json('tracks')->default('[]');
            $table->json('tracks_info')->default('{}');
            $table->boolean('is_major')->default(false);
            $table->boolean('is_in_use')->default(true);
            $table->boolean('is_signal_only')->default(false);
            $table->text('remarks')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->primary(['id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('hongon_hongon_stations');
    }
}
