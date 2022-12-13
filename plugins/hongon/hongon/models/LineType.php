<?php namespace Hongon\Hongon\Models;

use Model;
use Hongon\Hongon\Models\_Common;

/**
 * Model
 */
class LineType extends Model {

    public $table = 'hongon_hongon_line_types';
    protected $keyType = 'string';
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [
        'name_chi', 'name_eng', 'remarks', 'sort', 'map_color', 'map_thickness', 'hide_below_logzoom',
    ];
    protected $casts = [
        'hide_below_logzoom' => 'float',
    ];

    //Data validations
    public static $validations_new = [
        'name_chi' => 'required',
        'name_eng' => 'required',
    ];
    public static $validations_update = [
        'name_chi' => 'filled',
        'name_eng' => 'filled',
        'sort' => 'integer',
        'map_thickness' => 'integer',
        'hide_below_logzoom' => 'numeric',
    ];

    //Sorting & Filters
    public static $sort_default = 'sort,name_eng,name_chi';
    public static $sortable = ['sort', 'name_chi', 'name_eng'];

    public static function filters($query, $param){
        switch ($query){
        }
    }

    //Display Manipulation
    public static function display($results, $params){
        return $results;
    }

    //CUD Handlers
    public function onCreated($request){

    }
    public function onUpdated($request){
        
    }
    public function onDeleted($request){
        
    }

}
