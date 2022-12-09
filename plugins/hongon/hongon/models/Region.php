<?php namespace Hongon\Hongon\Models;

use Model;

/**
 * Model
 */
class Region extends Model {

    public $table = 'hongon_hongon_regions';
    protected $keyType = 'string';
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [
        'region_broader_id',
        'name_chi', 'name_eng', 'name_suffix_chi', 'name_suffix_eng', 'name_short_chi', 'name_short_eng',
        'remarks', 'sort',
    ];
    protected $casts = [
    ];
    protected $appends = ['name_chi_full', 'name_eng_full'];

    //Data validations
    public static $validations_update = [
        'region_broader_id' => 'exists:hongon_hongon_region_broaders,id',
        'sort' => 'integer',
    ];
    public static $validations_new = [
        'region_broader_id' => 'exists:hongon_hongon_region_broaders,id',
        'sort' => 'integer',
        'name_chi' => 'required',
        'name_eng' => 'required',
    ];

    //Append functions
    public function getNameChiFullAttribute(){
        return $this->name_chi . ($this->name_suffix_chi ? $this->name_suffix_chi : '');
    }

    public function getNameEngFullAttribute(){
        return $this->name_eng . ($this->name_suffix_eng ? (' '.$this->name_suffix_eng) : '');
    }

}
