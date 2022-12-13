<?php namespace Hongon\Hongon\Models;

use Model;
use Hongon\Hongon\Models\_Common;
use Hongon\Hongon\Models\RegionBroader;

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
    public static $validations_new = [
        'name_chi' => 'required',
        'name_eng' => 'required',
    ];
    public static $validations_update = [
        'name_chi' => 'filled',
        'name_eng' => 'filled',
        'region_broader_id' => 'exists:hongon_hongon_region_broaders,id',
        'sort' => 'integer',
    ];

    //Append functions
    public function getNameChiFullAttribute(){
        return $this->name_chi . ($this->name_suffix_chi ? $this->name_suffix_chi : '');
    }

    public function getNameEngFullAttribute(){
        return $this->name_eng . ($this->name_suffix_eng ? (' '.$this->name_suffix_eng) : '');
    }

    //Sorting & Filters
    public static $sort_default = 'sort,name_eng,name_chi';
    public static $sortable = [
        'sort', 'region_broader_id',
        'name_chi', 'name_eng', 'name_suffix_chi', 'name_suffix_eng', 'name_short_chi', 'name_short_eng'
    ];

    public static function filters($query, $param){
        switch ($query){
            case 'region_broader_id':
            return ['query' => 'region_broader_id = ?', 'params' => [$param]];
        }
    }

    //Display Manipulation
    public static function display($results, $params){
        $selecter_attributes = ['name_chi', 'name_eng', 'name_chi_full', 'name_eng_full', 'region_broader_id', 'region_id'];
        //selecter
        if (in_array('selecter', $params)){
            $results = _Common::keepOnlyAttributes($results, $selecter_attributes);
        }
        //region-broader
        if (in_array('region-broader', $params)){
            $region_broaders = RegionBroader::whereIn('id', _Common::getIDsFromResults($results, 'region_broader_id'))
                ->get()->toArray();
            $results = _Common::attachManyToOne($results, 'region_broader', $region_broaders);
            //region-broader,selecter
            if (in_array('selecter', $params)){
                $results = _Common::keepOnlyAttributes($results, $selecter_attributes, 'region_broader');
            }
        }
        //Return results
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
