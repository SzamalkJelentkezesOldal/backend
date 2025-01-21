<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SzakController extends Controller
{
    public function getSzakok() {
        $szakok = DB::table('szaks')
        ->select('id','elnevezes', 'portfolio', 'nappali')
        ->get();

        return $szakok;
    }

    public function getJelentkezokSzakra(String $szak){
        //Ki az aki paraméterben kapott szakra jelentkezett
        $result = DB::table('szaks as sz')
            ->join('jelentkezes as j', 'sz.id', '=', 'j.szak_id')
            ->join('jelentkezos as jo', 'j.jelentkezo_id', '=', 'jo.id')
            ->select('jo.id','jo.nev', 'jo.email', 'jo.tel', 'sz.elnevezes', 'sz.nappali')
            ->where('sz.elnevezes', '=',$szak)
            ->get();
        return $result;
    }

    public function jelentkezokSzamaSzakra(String $szak){
        //Ki az aki paraméterben kapott szakra jelentkezett
        $result = DB::table('szaks as sz')
            ->join('jelentkezes as j', 'sz.id', '=', 'j.szak_id')
            ->join('jelentkezos as jo', 'j.jelentkezo_id', '=', 'jo.id')
            ->selectRaw('sz.elnevezes, COUNT(*) as ennyien ')
            ->where('sz.elnevezes', '=',$szak)
            ->groupBy('sz.id')
            ->get();
        return $result;
    }



}
