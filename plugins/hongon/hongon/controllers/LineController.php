<?php

namespace Hongon\Hongon\Controllers;

use Backend\Classes\Controller;
use Hongon\Hongon\Models\_Common;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Hongon\Hongon\Models\LineSection;
use Hongon\Hongon\Models\Line;
use Hongon\Hongon\Models\LineType;
use Hongon\Hongon\Models\Operator;
use Hongon\Hongon\Models\OperatorType;

class LineController extends Controller{
    
    public function __construct(){
        parent::__construct();
    }

    private function decimal1($value){
        if (!is_numeric($value)) return null;
        return round($value, 1);
    }

    /**
     * GET api/hongon/line--stats
     */
    public function getLineStats(Request $request){

        //Get LineType Index
        $line_types = LineType::select(['id', 'name_chi', 'name_eng'])
        ->where('deleted_at', null)->orderBy('sort', 'asc')->get()->toArray();
        $line_type_index = [];
        foreach ($line_types as $i => $line_type){
            $line_types[$i]['total'] = 0;
            $line_type_index[$line_type['id']] = $i;
        }

        //Get LineType Index (By Line ID)
        $lines = Line::select(['id', 'line_type_id'])->where('deleted_at', null)->get()->toArray();
        $line_type_index_by_line_id = [];
        foreach ($lines as $i => $line){
            $index = $line_type_index[$line['line_type_id']] ?? null;
            $line_type_index_by_line_id[$line['id']] = $index;
        }

        //Get OperatorType Index
        $operator_types = OperatorType::select(['id', 'name_chi', 'name_eng'])
        ->where('deleted_at', null)->orderBy('sort', 'asc')->get()->toArray();
        $operator_type_index = [];
        foreach ($operator_types as $i => $operator_type){
            $operator_type_index[$operator_type['id']] = $i;
            $operator_types[$i]['total'] = 0;
            $operator_types[$i]['line_type'] = array_pad([], count($line_types), 0);
            $operator_types[$i]['operators'] = [];
        }

        //Get Operator Index (in OperatorType)
        $operators = Operator::select([
            'id', 'name_chi', 'name_eng', 'name_short_chi', 'name_short_eng', 'color', 'color_text', 'operator_type_id'
        ])
        ->where('deleted_at', null)->orderBy('sort', 'asc')->get()->toArray();
        $operator_index_in_type = [];
        $operator_type_index_by_operator_id = [];
        foreach ($operators as $i => $operator){
            $operator_type_id = $operator['operator_type_id'];
            $index = $operator_type_index[$operator_type_id];
            unset($operator['operator_type_id']);
            $operator['total'] = 0;
            $operator['line_type'] = array_pad([], count($line_types), 0);
            array_push($operator_types[$index]['operators'], $operator);
            $operator_index_in_type[$operator['id']] = count($operator_types[$index]['operators']) - 1;
            $operator_type_index_by_operator_id[$operator['id']] = $operator_type_index[$operator_type_id];
        }

        //Get LineSections
        $total = 0;
        $line_sections = LineSection::selectRaw("line_id, operator_id, \"_data\"->>'length_km' as \"length_km\"")
        ->get()->toArray();
        foreach ($line_sections as $line_section){
            $my_line_type_i = $line_type_index_by_line_id[$line_section['line_id']] ?? null;
            $my_operator_type_i = $operator_type_index_by_operator_id[$line_section['operator_id']] ?? null;
            $my_operator_i = $operator_index_in_type[$line_section['operator_id']] ?? null;
            $length_km = $line_section['length_km'] ?? 0;

            if ($my_line_type_i !== null && $my_operator_type_i !== null && $my_operator_i !== null){
                $total += $length_km;
                $line_types[$my_line_type_i]['total'] += $length_km;
                $operator_types[$my_operator_type_i]['total'] += $length_km;
                $operator_types[$my_operator_type_i]['line_type'][$my_line_type_i] += $length_km;
                $operator_types[$my_operator_type_i]['operators'][$my_operator_i]['total'] += $length_km;
                $operator_types[$my_operator_type_i]['operators'][$my_operator_i]['line_type'][$my_line_type_i] += $length_km;
            }
        }

        //Make Rounding
        $total = $this->decimal1($total);
        foreach ($line_types as $i => $line_type){
            $line_types[$i]['total']
            = $this->decimal1($line_types[$i]['total']);
        }
        foreach ($operator_types as $i => $operator_type){
            $operator_types[$i]['total']
            = $this->decimal1($operator_types[$i]['total']);
            foreach ($line_types as $j => $line_type){
                $operator_types[$i]['line_type'][$j]
                = $this->decimal1($operator_types[$i]['line_type'][$j]);
            }
            foreach ($operator_type['operators'] as $j => $operator){
                $operator_types[$i]['operators'][$j]['total']
                = $this->decimal1($operator_types[$i]['operators'][$j]['total']);
                foreach ($operator['line_type'] as $k => $line_type){
                    $operator_types[$i]['operators'][$j]['line_type'][$k]
                    = $this->decimal1($operator_types[$i]['operators'][$j]['line_type'][$k]);
                }
            }
        }
        
        /**
         * [Return Data]
         * operator_types[i].operators[j].line_type[k]
         * operator_types[i].operators[j].total
         * operator_types[i].operators[j].{id|name_chi|name_eng|name_short_chi|name_short_eng}
         * operator_types[i].line_type[j]
         * operator_types[i].total
         * operator_types[i].{id|name_chi|name_eng}
         * line_types[i].{id|name_chi|name_eng}
         * line_types[i].total
         * total
         */
        return response()->json([
            'total' => $total,
            'line_types' => $line_types,
            'operator_types' => $operator_types,
        ]);
        
    }

}
