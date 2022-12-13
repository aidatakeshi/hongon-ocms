<?php namespace Hongon\Hongon\Models;

use Model;
use Hongon\Hongon\Models\_Common;
use Hongon\Hongon\Models\Operator;
use Hongon\Hongon\Models\Region;

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
    public static $validations_new = [
        'name_chi' => 'required',
        'name_eng' => 'required',
    ];
    public static $validations_update = [
        'name_chi' => 'filled',
        'name_eng' => 'filled',
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
    
    //Sorting & Filters
    public static $sort_default = 'name_eng,name_chi';
    public static $sortable = ['name_chi', 'name_eng', 'major_operator_id', 'region_id', 'x', 'y', 'altitude_m'];

    public static function filters($query, $param){
        switch ($query){
            case 'region_id':
            return ['query' => 'region_id = ?', 'params' => [$param]];
            case 'major_operator_id':
            return ['query' => 'major_operator_id = ?', 'params' => [$param]];

            case 'is_signal_only':
            return ['query' => 'is_signal_only = TRUE', 'params' => []];
            case 'not_signal_only':
            return ['query' => 'is_signal_only = FALSE', 'params' => []];

            case 'major':
            return ['query' => 'major = TRUE', 'params' => []];
            case 'minor':
            return ['query' => 'major = FALSE', 'params' => []];

            case 'is_in_use':
            return ['query' => 'is_in_use = TRUE', 'params' => []];
            case 'not_in_use':
            return ['query' => 'is_in_use = FALSE', 'params' => []];

            case 'x_min':
            return ['query' => 'x >= ?', 'params' => [floatval($param)]];
            case 'x_max':
            return ['query' => 'x <= ?', 'params' => [floatval($param)]];
            case 'y_min':
            return ['query' => 'y >= ?', 'params' => [floatval($param)]];
            case 'y_max':
            return ['query' => 'y <= ?', 'params' => [floatval($param)]];

            case 'name_chi':
            return ['query' => 'LOWER(name_chi) LIKE LOWER(?)', 'params' => ["%$param%"]];
            case 'name_eng':
            return ['query' => 'LOWER(name_eng) LIKE LOWER(?)', 'params' => ["%$param%"]];
            case 'name':
            return [
                'query' => '(LOWER(name_chi) LIKE LOWER(?)) OR (LOWER(name_eng) LIKE LOWER(?))',
                'params' => ["%$param%", "%$param%"]
            ];
        }
    }

    //Display Manipulation
    public static function display($results, $params){
        $selecter_attributes = ['name_chi', 'name_eng', 'major_operator_id', 'region_id'];
        $selecter_attributes2 = array_merge($selecter_attributes, ['name_chi_full', 'name_eng_full']);
        //selecter
        if (in_array('selecter', $params)){
            $results = _Common::keepOnlyAttributes($results, $selecter_attributes);
        }
        //operator
        if (in_array('operator', $params)){
            $operators = Operator::whereIn('id', _Common::getIDsFromResults($results, 'major_operator_id'))
                ->orderBy('sort', 'asc')->get()->toArray();
            $results = _Common::attachManyToOne($results, 'major_operator', $operators);
            //operator,selecter
            if (in_array('selecter', $params)){
                $results = _Common::keepOnlyAttributes($results, $selecter_attributes, 'major_operator');
            }
        }
        //region
        if (in_array('region', $params)){
            $regions = Region::whereIn('id', _Common::getIDsFromResults($results, 'region_id'))
                ->orderBy('sort', 'asc')->get()->toArray();
            $results = _Common::attachManyToOne($results, 'region', $regions);
            //region,selecter
            if (in_array('selecter', $params)){
                $results = _Common::keepOnlyAttributes($results, $selecter_attributes2, 'region');
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
