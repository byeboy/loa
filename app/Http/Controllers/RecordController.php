<?php
/**
 * Created by PhpStorm.
 * User: Gilbert
 * Date: 17/3/28
 * Time: 上午10:59
 */

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class RecordController extends Controller
{
    public function create(Request $request) {
        $type = $request->json()->get('type');
        $count = $request->json()->get('count');

    }
}