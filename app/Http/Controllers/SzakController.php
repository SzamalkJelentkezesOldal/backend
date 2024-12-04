<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SzakController extends Controller
{
    public function getSzakok() {
        $szakok = DB::table('szaks')
        ->select('id','elnevezes', 'portfolio')
        ->get();

        return $szakok;
    }
}
