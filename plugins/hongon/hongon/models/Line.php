<?php namespace Hongon\Hongon\Models;

use Model;

/**
 * Model
 */
class Line extends Model {

    public $table = 'hongon_hongon_lines';
    protected $keyType = 'string';
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [
        'line_type_id', 'name_chi', 'name_eng', 'name_short_chi', 'name_short_eng',
        'remarks', '_data',
    ];
    protected $casts = [
        '_data' => 'json',
    ];

    //Data validations
    public static $validations_update = [
        'line_type_id' => 'exists:hongon_hongon_line_types,id',
        'sort' => 'integer',
        '_data' => 'json',
    ];
    public static $validations_new = [
        'line_type_id' => 'exists:hongon_hongon_line_types,id',
        'sort' => 'integer',
        'name_chi' => 'required',
        'name_eng' => 'required',
        '_data' => 'json',
    ];

}
