<?php namespace Hongon\Hongon\Models;

use Model;

/**
 * Model
 */
class RegionBroader extends Model {

    public $table = 'hongon_hongon_regions_broader';
    protected $keyType = 'string';
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [
        'region_broader_id',
        'name_chi', 'name_eng', 'name_short_chi', 'name_short_eng',
        'remarks', 'sort',
    ];
    protected $casts = [
    ];

    //Data validations
    public static $validations_update = [
        'sort' => 'integer',
    ];
    public static $validations_new = [
        'sort' => 'integer',
        'name_chi' => 'required',
        'name_eng' => 'required',
    ];

}
