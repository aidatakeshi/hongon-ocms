<?php namespace Hongon\Hongon\Models;

use Model;

/**
 * Model
 */
class RefImage extends Model {

    public $table = 'hongon_hongon_ref_images';
    protected $keyType = 'string';
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [
        'name', 'x_min', 'x_max', 'y_min', 'y_max', 'file_url', 'hide_below_logzoom', 'sort',
    ];
    protected $casts = [
        'x_min' => 'float',
        'y_min' => 'float',
        'x_max' => 'float',
        'y_max' => 'float',
        'hide_below_logzoom' => 'float',
    ];

    //Data validations
    public static $validations_update = [
        'x_min' => 'numeric',
        'y_min' => 'numeric',
        'x_max' => 'numeric',
        'y_max' => 'numeric',
        'sort' => 'integer',
    ];
    public static $validations_new = [
        'x_min' => 'numeric',
        'y_min' => 'numeric',
        'x_max' => 'numeric',
        'y_max' => 'numeric',
        'sort' => 'integer',
        'name' => 'required',
    ];
    
}
