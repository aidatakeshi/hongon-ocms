<?php namespace Hongon\Hongon\Models;

use Model;

/**
 * Model
 */
class Station extends Model {

    public $table = 'hongon_hongon_stations';
    protected $keyType = 'string';
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [
        'major_operator_id', 'region_id',
        'name_chi', 'name_eng', 'name_short_chi', 'name_short_eng', 'remarks',
        'x', 'y', 'altitude_m',
        'tracks', 'tracks_info',
        'is_major', 'is_in_use', 'is_signal_only',
    ];
    protected $casts = [
        'x' => 'float',
        'y' => 'float',
        'altitude_m' => 'float',
        'tracks' => 'array',
        'tracks_info' => 'object',
    ];

    //Data validations
    public static $validations_update = [
        'major_operator_id' => 'exists:hongon_hongon_operator_id,id',
        'region_id' => 'exists:hongon_hongon_regions,id',
        'x' => 'numeric',
        'y' => 'numeric',
        'altitude_m' => 'numeric',
        'tracks' => 'json',
        'track_info' => 'json',
        'is_major' => 'boolean',
        'is_in_use' => 'boolean',
        'is_signal_only' => 'boolean',
    ];
    public static $validations_new = [
        'major_operator_id' => 'exists:hongon_hongon_operator_id,id',
        'region_id' => 'exists:hongon_hongon_regions,id',
        'name_chi' => 'required',
        'name_eng' => 'required',
        'x' => 'numeric',
        'y' => 'numeric',
        'altitude_m' => 'numeric',
        'tracks' => 'json',
        'track_info' => 'json',
        'is_major' => 'boolean',
        'is_in_use' => 'boolean',
        'is_signal_only' => 'boolean',
    ];
    
}
