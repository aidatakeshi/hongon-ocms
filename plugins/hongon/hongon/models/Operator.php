<?php namespace Hongon\Hongon\Models;

use Model;
use Hongon\Hongon\Models\_Common;
use Hongon\Hongon\Models\OperatorType;

/**
 * Model
 */
class Operator extends Model {

    public $table = 'hongon_hongon_operators';
    protected $keyType = 'string';
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [
        'operator_type_id', 'name_chi', 'name_eng', 'name_short_chi', 'name_short_eng',
        'color', 'color_text', 'remarks', 'logo', 'sort',
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
        'operator_type_id' => 'exists:hongon_hongon_operator_types,id',
        'sort' => 'integer',
    ];

    //Sorting & Filters
    public static $sort_default = 'sort,name_eng,name_chi';
    public static $sortable = ['sort', 'name_chi', 'name_eng'];

    public static function filters($query, $param){
        switch ($query){
            case 'operator_type_id':
                return ['query' => 'operator_type_id = ?', 'params' => [$param]];
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
        $selecter_attributes = ['name_chi', 'name_eng', 'operator_type_id'];
        //selecter
        if (in_array('selecter', $params)){
            $results = _Common::keepOnlyAttributes($results, $selecter_attributes);
        }
        //operator-type
        if (in_array('operator-type', $params)){
            $operator_types = OperatorType::whereIn('id', _Common::getIDsFromResults($results, 'operator_type_id'))
                ->orderBy('sort', 'asc')->orderBy('name_eng', 'asc')->orderBy('name_chi', 'asc')
                ->get()->toArray();
            $results = _Common::attachManyToOne($results, 'operator_type', $operator_types);
            //operator,selecter
            if (in_array('selecter', $params)){
                $results = _Common::keepOnlyAttributes($results, $selecter_attributes, 'operator_type');
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
