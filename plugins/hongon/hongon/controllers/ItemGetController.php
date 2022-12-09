<?php

namespace Hongon\Hongon\Controllers;

use Backend\Classes\Controller;
use Hongon\Hongon\Models\_Common;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ItemGetController extends Controller{
    
    public function __construct(){
        parent::__construct();
    }

    /**
     * GET api/hongon/{type}
     * sort: according to $sortable, $sort_default in model class (starting with "-" means descending)
     * [filter]: according to filters() in model class
     * params: list of params for data display, comma separated
     * limit: no. of results per page
     * page: default is 1 (only when limit is set)
     * obj: if set, translate array to obj with id as attribute
     */
    public function getMultipleItems(Request $request, $type){
        
        //If class not found, 404
        $class = _Common::$class[$type] ?? null;
        if (!$class){
            return response()->json(['error' => 'Invalid Type'], 404);
        }

        //Start to Build Query
        $query = ($class)::where('deleted_at', null);

        //Apply Filters
        if (method_exists($class, 'filters')){
            $requests = $request->all();
            foreach ($requests as $key => $value){
                $where_statement = ($class)::filters($key, $value);
                if ($where_statement){
                    $query = $query->whereRaw($where_statement['query'], $where_statement['params']);
                }
            }
        }

        //Do Sorting
        $sort = $request->input('sort');
        if (!$sort) $sort = ($class)::$sort_default ?? null;
        if ($sort){
            $sort = explode(',', $sort);
            foreach ($sort as $sub_sort){
                switch(substr($sub_sort, 0, 1)){
                    case '-':
                        $sub_sort = substr($sub_sort, 1);
                        $direction = 'desc';
                        break;
                    case '+':
                        $sub_sort = substr($sub_sort, 1);
                        $direction = 'asc';
                        break;
                    default:
                        $direction = 'asc';
                };
                //If found in $sortable, do sorting
                if (in_array($sub_sort, ($class)::$sortable ?? [])){
                    $query = $query->orderBy($sub_sort, $direction);
                }
            }
        }

        //Make Count
        $count = $query->count();

        //Handle Limit & Page
        $limit = intval($request->input('limit'));
        if ($limit < 0){
            $limit = 0;
        }else if (!$limit){
            $limit = ($class)::$limit_default ?? 0;
        }
        if ($limit) $query = $query->limit($limit);

        $page = intval($request->input('page') ?? 1);
        if ($limit && $page && $count){
            $pages = ceil($count / $limit);
            if ($page < 1) $page = 1;
            else if ($page > $pages) $page = $pages;
            $query = $query->offset($limit * ($page - 1));
        }else{
            $pages = null;
            $page = null;
        }

        //Get Results (in array form)
        $results = $query->get()->toArray();

        //Call display($results, $params)
        if ($params = $request->input('params')){
            $params = explode(',', $params);
            if (method_exists($class, 'display')){
                $results = ($class)::display($results, $params) ?? $results;
            }
        }
        
        //Handle "obj" param
        $obj_as_result = false;
        if ($request->input('obj')){
            $obj_as_result = true;
            $results_obj = [];
            foreach ($results as $i => $result){
                $results_obj[$result->id] = $result;
                unset($results_obj[$result->id]['id']);
            }
        }

        //Return Result
        return response()->json([
            'count' => $count,
            'page' => $page,
            'pages' => $pages,
            'limit' => $limit,
            'data' => $obj_as_result ? $results_obj : $results,
        ]);

    }

    /**
     * GET api/hongon/{type}/{id}
     * params: list of params for data display, comma separated
     */
    public function getOneItem(Request $request, $type, $id, $is201 = false){
        
        //If class not found, 404
        $class = _Common::$class[$type] ?? null;
        if (!$class){
            return response()->json(['error' => 'Invalid Type'], 404);
        }

        //If item not found, 404
        $item = ($class)::where('id', $id)->where('deleted_at', null)->first();
        if (!$item){
            return response()->json(['error' => 'Item Not Found'], 404);
        }
        $item = $item->toArray();

        //Call display($results, $params)
        if ($params = $request->input('params')){
            $params = explode(',', $params);
            if (method_exists($class, 'display')){
                $item = (($class)::display([$item], $params) ?? [$item])[0];
            }
        }

        //Return Data
        return response()->json([
            'data' => $item,
        ], $is201 ? 201 : 200);
        
    }

}
