<?php namespace Hongon\Hongon\Models;

use Model;
use Hongon\Hongon\Models\_Common;
use Hongon\Hongon\Models\LineType;
use Hongon\Hongon\Models\LineSection;
use Hongon\Hongon\Models\Operator;
use Hongon\Hongon\Models\Station;

/**
 * Model
 */
class Line extends Model {

    public $table = 'hongon_hongon_lines';
    protected $keyType = 'string';
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [
        'line_type_id', 'name_chi', 'name_eng', 'name_short_chi', 'name_short_eng',
        'remarks',
    ];
    protected $casts = [
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
        'line_type_id' => 'exists:hongon_hongon_line_types,id',
        '_data' => 'json',
    ];

    //Sorting & Filters
    public static $sort_default = 'name_eng,name_chi';
    public static $sortable = ['name_chi', 'name_eng', 'line_type_id'];

    public static function filters($query, $param){
        switch ($query){
            case 'line_type_id':
                return ['query' => 'line_type_id = ?', 'params' => [$param]];
            case 'operator_id':
                return ['query' => '_data->>\'operator_ids\' LIKE ?', 'params' => ["%|$param%"]];
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
        $selecter_attributes = ['name_chi', 'name_eng', 'name_short_chi', 'name_short_eng', 'line_type_id', 'operator_id'];
        //selecter
        if (in_array('selecter', $params)){
            $results = _Common::keepOnlyAttributes($results, $selecter_attributes);
        }
        //line-type
        if (in_array('line-type', $params)){
            $line_types = LineType::whereIn('id', _Common::getIDsFromResults($results, 'line_type_id'))
                ->get()->toArray();
            $results = _Common::attachManyToOne($results, 'line_type', $line_types);
            //line-type,selecter
            if (in_array('selecter', $params)){
                $results = _Common::keepOnlyAttributes($results, $selecter_attributes, 'line_type');
            }
        }
        //line-section
        if (in_array('line-section', $params)){
            if (true){
                $line_sections = LineSection::where('deleted_at', null)
                ->whereIn('line_id', _Common::getIDsFromResults($results))
                    ->orderBy('sort_in_line', 'asc')->get()->toArray();
                foreach ($line_sections as $i => $line_section){
                    unset($line_sections[$i]['_data']['station_ids']);
                }
                $results = _Common::attachOneToMany($results, 'line', $line_sections, 'line_section');
            }
            //line-section,operator
            if (in_array('operator', $params)){
                $operator_ids = [];
                foreach ($results as $i => $result){
                    foreach ($result['line_section'] as $j => $section) array_push($operator_ids, $section['operator_id']);
                }
                $operators = Operator::whereIn('id', $operator_ids)->get()->toArray();
                $operators = _Common::resultArrayToObject($operators);
                $operators = _Common::keepOnlyAttributes($operators, [
                    'name_chi', 'name_eng', 'name_short_chi', 'name_short_eng', 'operator_type_id',
                ]);
                foreach ($results as $i => $result){
                    foreach ($result['line_section'] as $j => $section){
                        $results[$i]['line_section'][$j]['operator'] = $operators[$section['operator_id']] ?? null;
                        unset($results[$i]['line_section'][$j]['operator_id']);
                    }
                }
            }
            //line-section,segment
            if (!in_array('segment', $params)){
                foreach ($results as $i => $result){
                    foreach ($results[$i]['line_section'] as $j => $section){
                        foreach ($section['stations'] as $k => $station){
                            unset($results[$i]['line_section'][$j]['stations'][$k]['segments']);
                        }
                    }
                }
            }
            //line-section,station-info
            if (in_array('station-info', $params)){
                $station_ids = [];
                foreach ($results as $i => $result){
                    foreach ($result['line_section'] as $j => $section){
                        foreach ($section['stations'] as $k => $station){
                            array_push($station_ids, $station['station_id']);
                        }
                    }
                }
                $stations = Station::whereIn('id', $station_ids)->get()->toArray();
                $stations = _Common::resultArrayToObject($stations);
                $stations = _Common::keepOnlyAttributes($stations, [
                    'name_chi', 'name_eng', 'name_short_chi', 'name_short_eng', 'region_id',
                ]);
                foreach ($results as $i => $result){
                    foreach ($result['line_section'] as $j => $section){
                        foreach ($section['stations'] as $k => $station){
                            if ($station_info = $stations[$station['station_id']] ?? null){
                                foreach ($station_info as $n => $v){
                                    $results[$i]['line_section'][$j]['stations'][$k][$n] = $v;
                                }
                            }
                        }
                    }
                }
            }
            //line-section,selecter
            if (in_array('selecter', $params)){
                foreach ($results as $i => $result){
                    $results[$i]['line_section'] = _Common::keepOnlyAttributes($result['line_section'], $selecter_attributes);
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

    //Update Data
    public function updateLineData(){
        $data = [
            'length_km' => 0,
            'x_min' => null,
            'x_max' => null,
            'y_min' => null,
            'y_max' => null,
            'operator_ids' => '',
        ];
        $x = [];
        $y = [];
        $line_sections = LineSection::where('deleted_at', null)->where('line_id', $this->id)->get();
        foreach ($line_sections as $section){
            $data['length_km'] += ($section->_data['length_km'] ?? 0);
            if (($val = $section->_data['x_min'] ?? null) !== null) array_push($x, $val);
            if (($val = $section->_data['x_max'] ?? null) !== null) array_push($x, $val);
            if (($val = $section->_data['y_min'] ?? null) !== null) array_push($y, $val);
            if (($val = $section->_data['y_max'] ?? null) !== null) array_push($y, $val);
            $data['operator_ids'] .= '|' . $section->operator_id;
        }
        $data['x_min'] = min($x);
        $data['x_max'] = max($x);
        $data['y_min'] = min($y);
        $data['y_max'] = max($y);
        $this->_data = $data;
        $this->save();
    }

}
