<?php namespace Hongon\Hongon\Models;

use Model;
use Hongon\Hongon\Models\_Common;
use Hongon\Hongon\Models\Region;

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
    public static $validations_new = [
        'name_chi' => 'required',
        'name_eng' => 'required',
    ];
    public static $validations_update = [
        'name_chi' => 'filled',
        'name_eng' => 'filled',
        'sort' => 'integer',
    ];

    //Sorting & Filters
    public static $sort_default = 'sort,name_eng,name_chi';
    public static $sortable = [
        'sort', 'region_broader_id',
        'name_chi', 'name_eng', 'name_suffix_chi', 'name_suffix_eng', 'name_short_chi', 'name_short_eng'
    ];

    public static function filters($query, $param){
        switch ($query){
        }
    }

    //Display Manipulation
    public static function display($results, $params){
        $selecter_attributes = ['name_chi', 'name_eng', 'name_chi_full', 'name_eng_full'];
        //selecter
        if (in_array('selecter', $params)){
            $results = _Common::keepOnlyAttributes($results, $selecter_attributes);
        }
        //region
        if (in_array('region', $params)){
            $regions = Region::where('deleted_at', null)
            ->whereIn('region_broader_id', _Common::getIDsFromResults($results))
                ->orderBy('sort', 'asc')->get()->toArray();
            $results = _Common::attachOneToMany($results, 'region_broader', $regions, 'region');
            //region,selecter
            if (in_array('selecter', $params)){
                foreach ($results as $i => $result){
                    $results[$i]['region'] = _Common::keepOnlyAttributes($result['region'], $selecter_attributes);
                }
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
