<?php namespace Hongon\Hongon\Models;

use Model;
use Hongon\Hongon\Models\_Common;
use Hongon\Hongon\Models\Line;
use Hongon\Hongon\Models\LineType;
use Hongon\Hongon\Models\Operator;
use Hongon\Hongon\Models\Station;
use Hongon\Hongon\Models\Region;

/**
 * Model
 */
class LineSection extends Model {

    public $table = 'hongon_hongon_line_sections';
    protected $keyType = 'string';
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [
        'line_id', 'operator_id',
        'name_chi', 'name_eng', 'name_short_chi', 'name_short_eng',
        'sort_in_line',
        'color', 'color_text', 'remarks', 'max_speed_kph',
        'stations', '_data',
    ];
    protected $casts = [
        'stations' => 'json',
        '_data' => 'json',
    ];

    //Data validations
    public static $validations_new = [
        'name_chi' => 'required',
        'name_eng' => 'required',
    ];
    public static $validations_update = [
        'name_chi' => 'filled',
        'name_eng' => 'filled',
        'line_id' => 'exists:hongon_hongon_lines,id',
        'operator_id' => 'exists:hongon_hongon_operators,id',
        'max_speed_kph' => 'integer',
        'sort_in_line' => 'integer',
        'stations' => 'json',
        '_data' => 'json',
    ];

    //Sorting & Filters
    public static $sort_default = 'sort_in_line';
    public static $sortable = ['sort_in_line', 'name_chi', 'name_eng', 'line_id', 'operator_id'];

    public static function filters($query, $param){
        switch ($query){
            case 'line_id':
                return ['query' => 'line_id = ?', 'params' => [$param]];
            case 'operator_id':
                return ['query' => 'operator_id = ?', 'params' => [$param]];
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
        $selecter_attributes = [
            'name_chi', 'name_eng', 'name_short_chi', 'name_short_eng',
            'line_id', 'line_type_id', 'operator_id'
        ];
        //(Remove _data.station_ids)
        foreach ($results as $i => $result){
            unset($results[$i]['_data']['station_ids']);
        }
        //selecter
        if (in_array('selecter', $params)){
            $results = _Common::keepOnlyAttributes($results, $selecter_attributes);
        }
        //line
        if (in_array('line', $params)){
            $lines = Line::whereIn('id', _Common::getIDsFromResults($results, 'line_id'))
                ->get()->toArray();
            $results = _Common::attachManyToOne($results, 'line', $lines);
            //line,selecter
            if (in_array('selecter', $params)){
                $results = _Common::keepOnlyAttributes($results, $selecter_attributes, 'line');
            }
        }
        //operator
        if (in_array('operator', $params)){
            $operators = Operator::whereIn('id', _Common::getIDsFromResults($results, 'operator_id'))
                ->get()->toArray();
            $results = _Common::attachManyToOne($results, 'operator', $operators);
            //operator,selecter
            if (in_array('selecter', $params)){
                $results = _Common::keepOnlyAttributes($results, $selecter_attributes, 'operator');
            }
        }
        //segment
        if (!in_array('segment', $params)){
            foreach ($results as $i => $result){
                if (isset($result['stations'])){
                    foreach ($result['stations'] as $j => $station){
                        unset($results[$i]['stations'][$j]['segments']);
                    }
                }
            }
        }
        //station | station-info
        if (in_array('station-info', $params) || in_array('station', $params)){
            $station_ids = [];
            foreach ($results as $i => $result){
                foreach ($result['stations'] as $j => $station){
                    array_push($station_ids, $station['station_id']);
                }
            }
            $stations = Station::whereIn('id', $station_ids)->get()->toArray();
            $stations = _Common::resultArrayToObject($stations);
            //*station-info
            if (in_array('station-info', $params)){
                $stations = _Common::keepOnlyAttributes($stations, [
                    'name_chi', 'name_eng', 'name_short_chi', 'name_short_eng', 'region_id',
                ]);
                foreach ($results as $i => $result){
                    foreach ($result['stations'] as $j => $station){
                        if ($station_info = $stations[$station['station_id']] ?? null){
                            foreach ($station_info as $n => $v){
                                $results[$i]['stations'][$j][$n] = $v;
                            }
                        }
                    }
                }
            }
            //*station
            else{
                foreach ($results as $i => $result){
                    foreach ($result['stations'] as $j => $station){
                        $results[$i]['stations'][$j]['station'] = $stations[$station['station_id']] ?? null;
                        unset($results[$i]['stations'][$j]['station_id']);
                    }
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
