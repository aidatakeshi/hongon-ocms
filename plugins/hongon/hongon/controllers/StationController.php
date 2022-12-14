<?php

namespace Hongon\Hongon\Controllers;

use Backend\Classes\Controller;
use Hongon\Hongon\Models\_Common;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Hongon\Hongon\Models\Station;
use Hongon\Hongon\Models\Region;
use Hongon\Hongon\Models\Operator;

class StationController extends Controller{
    
    public function __construct(){
        parent::__construct();
    }

    /**
     * GET api/hongon/stations/name-similar
     * keyword: Name keyword
     * limit: Results limit (default 10)
     */
    public function getSimilarStationNames(Request $request){
        
        $keyword = $request->input('keyword') ?? null;
        $limit = intval($request->input('limit'));
        if (!$limit) $limit = 10; //Default value
        if (!$keyword){
            return response()->json(['error' => 'Keyword Not Specified'], 404);
        }

        //Make Query
        $p0 = "$keyword";
        $p1 = "$keyword%";
        $p2 = "%$keyword%";

        $results = Station::selectRaw('id, major_operator_id, region_id, name_chi, name_eng,
        (name_chi ilike ? or name_eng ilike ?) as match_begin,
        (name_chi ilike ? or name_eng ilike ?) as match_all,
        length(concat(name_chi, name_eng)) as length_total',
        [$p1, $p1, $p0, $p0])
        ->whereRaw('name_chi ilike ? or name_eng ilike ?', [$p2, $p2])
        ->where('deleted_at', null)
        ->orderBy('match_all', 'desc')->orderBy('match_begin', 'desc')->orderBy('length_total', 'asc')
        ->limit($limit)->get();

        //Get Region Info & Operator Info
        $region_ids = [];
        $operator_ids = [];
        foreach ($results as $i => $result){
            foreach (['match_begin', 'match_all', 'length_total'] as $k) unset($results[$i][$k]);
            array_push($region_ids, $result['region_id']);
            array_push($operator_ids, $result['major_operator_id']);
        }
        $region_ids = array_unique($region_ids);
        $operator_ids = array_unique($operator_ids);
        $regions = Region::where('deleted_at', null)->whereIn('id', $region_ids)->get();
        $operators = Operator::where('deleted_at', null)->whereIn('id', $operator_ids)->get();
        $regions_by_id = [];
        $operators_by_id = [];
        foreach ($regions as $region) $regions_by_id[$region['id']] = [
            'id' => $region['id'],
            'name_chi' => $region['name_chi'],
            'name_eng' => $region['name_eng'],
            'name_chi_full' => $region['name_chi_full'],
            'name_eng_full' => $region['name_eng_full'],
        ];
        foreach ($operators as $operator) $operators_by_id[$operator['id']] = [
            'id' => $operator['id'],
            'name_chi' => $operator['name_chi'],
            'name_eng' => $operator['name_eng'],
            'name_short_chi' => $operator['name_short_chi'],
            'name_short_eng' => $operator['name_short_eng'],
        ];
        foreach ($results as $i => $result){
            $results[$i]['region'] = $regions_by_id[$results[$i]['region_id']] ?? null;
            $results[$i]['major_operator'] = $operators_by_id[$results[$i]['major_operator_id']] ?? null;
            unset($results[$i]['region_id']);
            unset($results[$i]['major_operator_id']);
        }

        //Return Results
        return response()->json(['data' => $results]);
        
    }

}
