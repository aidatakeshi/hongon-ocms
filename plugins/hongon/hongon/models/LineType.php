<?php namespace Hongon\Hongon\Models;

use Model;

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
    public static $validations_update = [
        'sort' => 'integer',
        'map_thickness' => 'integer',
        'hide_below_logzoom' => 'numeric',
    ];
    public static $validations_new = [
        'sort' => 'integer',
        'map_thickness' => 'integer',
        'hide_below_logzoom' => 'numeric',
        'name_chi' => 'required',
        'name_eng' => 'required',
    ];

}
