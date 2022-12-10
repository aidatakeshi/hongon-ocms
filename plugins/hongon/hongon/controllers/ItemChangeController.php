<?php

namespace Hongon\Hongon\Controllers;

use Backend\Classes\Controller;
use Hongon\Hongon\Models\_Common;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use Hongon\Hongon\Controllers\ItemGetController;

class ItemChangeController extends Controller{
    
    public function __construct(){
        parent::__construct();
    }

    /**
     * POST api/hongon/{type}
     * [attribute]: data attribute of new item
     * _get: If not null, get item immediately after creation (same output as GET api/hongon/{type}/{id})
     * _params: list of params for data display, comma separated (only if "_get" is set)
     */
    public function newItem(Request $request, $type){
        
        //If class not found, 404
        $class = _Common::$classes[$type] ?? null;
        if (!$class) return response()->json(['error' => 'Invalid Type'], 404);

        //Do validation, if failed, 400
        $validation_errors = _Common::getValidationErrors($request->all(), $class, $isNew = true);
        if ($validation_errors){
            return response()->json(['error' => 'Validation Errors', 'details' => $validation_errors], 400);
        }

        //Prepare Data
        $data = $request->all();
        $new_sort = _Common::getNewSortValue($class);

        //Proceed
        DB::beginTransaction();
        try{
            //Create new item
            $item = new $class;
            $item->id = _Common::getNewUUID($class);
            if ($new_sort !== null) $item->sort = $new_sort;
            $item->save();
            $item->update($data);
            //Call onCreated($request) on new item
            if (method_exists($class, 'onCreated')){
                $item->onCreated($request);
            }
        }catch(\Exception $e){
            DB::rollBack();
            die();
        }
        DB::commit();
        
        //If _get is set...
        if ($request->input('_get')){
            $itemGetController = new ItemGetController();
            return $itemGetController->getOneItem($request, $type, $item->id, $is201 = true);
        }
        
        return response()->json(['id' => $item->id], 201);
        
    }

    /**
     * POST api/hongon/{type}/{id}
     * [attribute]: data attribute that is to be changed for the duplicated item
     * _get: If not null, get item immediately after creation (same output as GET api/hongon/{type}/{id})
     * _params: list of params for data display, comma separated (only if "_get" is set)
     */
    public function duplicateItem(Request $request, $type, $id){
        
        //If class not found, 404
        $class = _Common::$classes[$type] ?? null;
        if (!$class) return response()->json(['error' => 'Invalid Type'], 404);
        //If item not found, 404
        $item = _Common::findItem($class, $id);
        if (!$item) return response()->json(['error' => 'Item Not Found'], 404);

        //Do validation, if failed, 400
        $validation_errors = _Common::getValidationErrors($request->all(), $class, $isNew = false);
        if ($validation_errors){
            return response()->json(['error' => 'Validation Errors', 'details' => $validation_errors], 400);
        }

        //Prepare Data
        $data = array_merge($item, $request->all());
        $new_sort = _Common::getNewSortValue($class);

        //Proceed
        DB::beginTransaction();
        try{
            //Create new item
            $item = new $class;
            $item->id = _Common::getNewUUID($class);
            if ($new_sort !== null) $item->sort = $new_sort;
            $item->save();
            $item->update($data);
            //Call onCreated($request) on new item
            if (method_exists($class, 'onCreated')){
                $item->onCreated($request);
            }
        }catch(\Exception $e){
            DB::rollBack();
            die();
        }
        DB::commit();
        
        //If _get is set...
        if ($request->input('_get')){
            $itemGetController = new ItemGetController();
            return $itemGetController->getOneItem($request, $type, $item->id, $is201 = true);
        }
        
        return response()->json(['id' => $item->id], 201);
        
    }

    /**
     * PATCH api/hongon/{type}/{id}
     * [attribute]: data attribute that is to be changed for the existing item
     * _get: If not null, get item immediately after creation (same output as GET api/hongon/{type}/{id})
     * _params: list of params for data display, comma separated (only if "_get" is set)
     */
    public function updateItem(Request $request, $type, $id){
        
        //If class not found, 404
        $class = _Common::$classes[$type] ?? null;
        if (!$class) return response()->json(['error' => 'Invalid Type'], 404);
        //If item not found, 404
        $item = _Common::findItem($class, $id);
        if (!$item) return response()->json(['error' => 'Item Not Found'], 404);

        //Do validation, if failed, 400
        $validation_errors = _Common::getValidationErrors($request->all(), $class, $isNew = false);
        if ($validation_errors){
            return response()->json(['error' => 'Validation Errors', 'details' => $validation_errors], 400);
        }

        //Prepare Data
        $data = $request->all();

        //Proceed
        DB::beginTransaction();
        try{
            //Make update
            $item->update($data);
            //Call onUpdated($request) on the item
            if (method_exists($class, 'onUpdated')){
                $item->onUpdated($request);
            }
        }catch(\Exception $e){
            DB::rollBack();
            die();
        }
        DB::commit();
        
        //If _get is set...
        if ($request->input('_get')){
            $itemGetController = new ItemGetController();
            return $itemGetController->getOneItem($request, $type, $item->id);
        }
        
        return response()->json((object)[], 200);
        
    }

    /**
     * PATCH api/hongon/{type}
     * items: array of items
     * items[i].id: ID if an item
     * items[i].{attribute}: data attribute that is to be changed for the existing item
     * _get: If not null, get item immediately after creation (same output as GET api/hongon/{type}/{id})
     * _params: list of params for data display, comma separated (only if "_get" is set)
     */
    public function updateItems(Request $request, $type){
        
        //If class not found, 404
        $class = _Common::$classes[$type] ?? null;
        if (!$class) return response()->json(['error' => 'Invalid Type'], 404);

        //Validate "items" request
        $req_items = $request->input('items');
        if (!is_array($req_items)){
            return response()->json(['error' => 'Invalid Items'], 400);
        }else if (count(array_filter(array_keys($req_items), 'is_string'))){
            return response()->json(['error' => 'Invalid Items'], 400);
        }
        foreach ($req_items as $req_item){
            if (!is_array($req_item)){
                return response()->json(['error' => 'Invalid Items'], 400);
            }
        }

        //If any of the items not found, 404
        $items = [];
        foreach ($req_items as $i => $req_item){
            $id = $req_item['id'];
            $item = _Common::findItem($class, $id);
            if (!$item) return response()->json(['error' => 'Item Not Found', 'index' => $i], 404);
            array_push($items, $item);
        }

        //Do validation, if failed, 400
        $validation_errors_array = array_fill(0, count($req_items), null);
        $has_validation_errors = false;
        foreach ($req_items as $i => $req_item){
            $validation_errors = _Common::getValidationErrors($req_item, $class, $isNew = false);
            if ($validation_errors){
                $validation_errors_array[$i] = $validation_errors;
                $has_validation_errors = true;
            }
        }
        if ($has_validation_errors){
            return response()->json(['error' => 'Validation Errors', 'details' => $validation_errors_array], 400);
        }

        //Proceed
        DB::beginTransaction();
        try{
            foreach ($req_items as $i => $data){
                //Make updates
                $items[$i]->update($data);
                //Call onUpdated($request) on the item
                if (method_exists($class, 'onUpdated')){
                    $items[$i]->onUpdated($data);
                }
                $items[$i] = $items[$i]->toArray();
            }
        }catch(\Exception $e){
            DB::rollBack();
            die();
        }
        DB::commit();
        
        //If _get is set...
        if ($request->input('_get')){
            if ($params = $request->input('params')){
                $params = explode(',', $params);
                if (method_exists($class, 'display')){
                    $items = ($class)::display($items, $params) ?? $items;
                }
            }
            return response()->json([
                'data' => $items,
            ], 200);
        }
        
        return response()->json((object)[], 200);
        
    }

    /**
     * DELETE api/hongon/{type}/{id}
     */
    public function removeItem(Request $request, $type, $id){
        
        //If class not found, 404
        $class = _Common::$classes[$type] ?? null;
        if (!$class) return response()->json(['error' => 'Invalid Type'], 404);
        //If item not found, 404
        $item = _Common::findItem($class, $id);
        if (!$item) return response()->json(['error' => 'Item Not Found'], 404);
        
        //If _get is set...
        if ($request->input('_get')){
            $itemGetController = new ItemGetController();
            $get_response = $itemGetController->getOneItem($request, $type, $item->id);
        }

        //Proceed
        DB::beginTransaction();
        try{
            //Call onDeleted($request) on the item
            if (method_exists($class, 'onDeleted')){
                $item->onDeleted($request);
            }
            //Make deletion
            $item->deleted_at = date('Y-m-d H:i:s');
            $item->save();
        }catch(\Exception $e){
            DB::rollBack();
            die();
        }
        DB::commit();

        //Make deletion
        $item->deleted_at = date('Y-m-d H:i:s');
        $item->save();

        //Return data
        if ($request->input('_get')){
            return $get_response;
        }
        return response()->json((object)[]);
        
    }

    /**
     * PUT api/hongon/{type}
     * ids: IDs of items to be reordered, in array
     */
    public function reorderItems(Request $request, $type){
        
        //If class not found, 404
        $class = _Common::$classes[$type] ?? null;
        if (!$class) return response()->json(['error' => 'Invalid Type'], 404);

        //If class not sortable, 400
        if (!in_array('sort', ($class)::$sortable ?? [])){
            return response()->json(['error' => 'Cannot Reorder'], 400);
        }

        //Validate "ids" request
        $ids = $request->input('ids');
        if (!is_array($ids)){
            return response()->json(['error' => 'Invalid IDs'], 400);
        }

        //Get sort values. If any of the items not found, 404
        $sort_values = [];
        $items = [];
        foreach ($ids as $i => $id){
            $item = _Common::findItem($class, $id);
            if (!$item) return response()->json(['error' => 'Item Not Found', 'index' => $i], 404);
            array_push($items, $item);
            array_push($sort_values, $item['sort']);
        }

        sort($sort_values);
        $data = [];

        //Reassign sort values
        DB::beginTransaction();
        try{
            foreach ($items as $i => $item){
                //Make updates
                $items[$i]->sort = $sort_values[$i];
                $items[$i]->save();
                array_push($data, ['id' => $item['id'], 'sort' => $sort_values[$i]]);
            }
        }catch(\Exception $e){
            DB::rollBack();
            die();
        }
        DB::commit();

        //End
        return response()->json(['data' => $data]);
        
    }

}
