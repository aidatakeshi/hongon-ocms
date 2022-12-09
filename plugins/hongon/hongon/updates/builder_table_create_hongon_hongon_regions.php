<?php namespace Hongon\Hongon\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateHongonHongonRegions extends Migration
{
    public function up()
    {
        Schema::create('hongon_hongon_regions', function($table)
        {
            $table->engine = 'InnoDB';
            $table->string('id');
            $table->string('region_broader_id')->nullable();
            $table->text('name_chi');
            $table->text('name_eng');
            $table->text('name_suffix_chi')->nullable();
            $table->text('name_suffix_eng')->nullable();
            $table->text('name_short_chi')->nullable();
            $table->text('name_short_eng')->nullable();
            $table->text('remarks')->nullable();
            $table->bigInteger('sort');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->primary(['id']);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('hongon_hongon_regions');
    }
}
