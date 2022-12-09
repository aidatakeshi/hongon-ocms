<?php namespace Hongon\Hongon\Models;

use Model;

/**
 * Model
 */
class LineSection extends Model {

    public $table = 'hongon_hongon_line_sections';
    protected $keyType = 'string';
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [
        'line_id', 'operator_id',
        'name_chi', 'name_eng', 'name_short_chi', 'name_short_eng',
        'color', 'color_text', 'remarks', 'max_speed_kph',
        'stations', '_data',
    ];
    protected $casts = [
        'stations' => 'json',
        '_data' => 'json',
    ];

    //Data validations
    public static $validations_update = [
        'line_id' => 'exists:hongon_hongon_lines,id',
        'operator_id' => 'exists:hongon_hongon_operators,id',
        'max_speed_kph' => 'integer',
        'sort' => 'integer',
        'stations' => 'json',
        '_data' => 'json',
    ];
    public static $validations_new = [
        'line_id' => 'exists:hongon_hongon_lines,id',
        'operator_id' => 'exists:hongon_hongon_operators,id',
        'name_chi' => 'required',
        'name_eng' => 'required',
        'max_speed_kph' => 'integer',
        'sort' => 'integer',
        'stations' => 'json',
        '_data' => 'json',
    ];
    
}
