<?php namespace Hongon\Hongon\Models;

use Model;
use Hongon\Hongon\Models\_Common;
use Hongon\Hongon\Models\Operator;

/**
 * Model
 */
class OperatorType extends Model {

    public $table = 'hongon_hongon_operator_types';
    protected $keyType = 'string';
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [
        'name_chi', 'name_eng', 'remarks', 'sort', 'map_color', 'map_thickness', 'hide_below_logzoom',
    ];
    protected $casts = [
        'hide_below_logzoom' => 'float',
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
        'map_thickness' => 'integer',
        'hide_below_logzoom' => 'numeric',
    ];
    
    //Sorting & Filters
    public static $sort_default = 'sort,name_eng,name_chi';
    public static $sortable = ['sort', 'name_chi', 'name_eng'];

    public static function filters($query, $param){
        switch ($query){
            case 'name_chi':
                return ['query' => 'LOWER(name_chi) LIKE LOWER(?)', 'params' => ["%$param%"]];
            case 'name_eng':
                return ['query' => 'LOWER(name_eng) LIKE LOWER(?)', 'params' => ["%$param%"]];
            case 'name':
                return [
                    'query' => 'LOWER(name_chi) LIKE LOWER(?) OR LOWER(name_eng) LIKE LOWER(?)',
                    'params' => ["%$param%", "%$param%"]
                ];
        }
    }

    //Display Manipulation
    public static function display($results, $params){
        $selecter_attributes = ['name_chi', 'name_eng'];
        //selecter
        if (in_array('selecter', $params)){
            $results = _Common::keepOnlyAttributes($results, $selecter_attributes);
        }
        //operator
        if (in_array('operator', $params)){
            $operators = Operator::where('deleted_at', null)
            ->whereIn('operator_type_id', _Common::getIDsFromResults($results))
                ->orderBy('sort', 'asc')->orderBy('name_eng', 'asc')->orderBy('name_chi', 'asc')
                ->get()->toArray();
            $results = _Common::attachOneToMany($results, 'operator_type', $operators, 'operator');
            //operator,selecter
            if (in_array('selecter', $params)){
                foreach ($results as $i => $result){
                    $results[$i]['operator'] = _Common::keepOnlyAttributes($result['operator'], $selecter_attributes);
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
