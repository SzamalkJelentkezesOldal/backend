<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DokumentumTipusController extends Controller
{
    public function getDokumentumTipusok() {
        $dokumentum_tipusok = DB::table('dokumentum_tipuses')
        ->select('id','elnevezes',)
        ->get();

        return $dokumentum_tipusok;
    }
}
