<?php

namespace Hongon\Hongon\Controllers;

use Backend\Classes\Controller;
use Hongon\Hongon\Models\_Common;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
class DummyController extends Controller{
    
    public function __construct(){
        parent::__construct();
    }

    public function dummy(Request $request){
        
        return response()->json((object)[]);
        
    }

}
