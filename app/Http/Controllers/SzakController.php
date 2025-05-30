<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Szak;

class SzakController extends Controller
{
    public function getSzakok()
    {
        $szakok = DB::table('szaks')
            ->select('id', 'elnevezes', 'portfolio', 'nappali')
            ->get();

        return $szakok;
    }

    public function postSzak(Request $request)
    {
        $ellenorzottAdatok = $request->validate([
            'elnevezes' => 'required|string|max:255',
            'portfolio' => 'required|boolean',
            'nappali' => 'required|boolean',
        ]);

   
        $ujSzak = Szak::create([
            'elnevezes' => $ellenorzottAdatok['elnevezes'],
            'portfolio' => $ellenorzottAdatok['portfolio'],
            'nappali' => $ellenorzottAdatok['nappali'],
        ]);

        return response()->json($ujSzak);
    }

    public function szakDelete(string $id)
    {
        Szak::find($id)->delete();
    }

    public function szakPatch(Request $request, string $id)
    {
        $record = Szak::find($id);
        $record->fill($request->all());
        $record->save();
    }

    public function getJelentkezokSzakra(String $szak)
    {
        //Ki az aki paraméterben kapott szakra jelentkezett
        $result = DB::table('szaks as sz')
            ->join('jelentkezes as j', 'sz.id', '=', 'j.szak_id')
            ->join('jelentkezos as jo', 'j.jelentkezo_id', '=', 'jo.id')
            ->select('jo.id', 'jo.nev', 'jo.email', 'jo.tel', 'sz.elnevezes', 'sz.nappali')
            ->where('sz.elnevezes', '=', $szak)
            ->get();
        return $result;
    }

    public function jelentkezokSzamaSzakra($szak_id)
    {
        $result = DB::table('szaks as sz')
            ->join('jelentkezes as j', 'sz.id', '=', 'j.szak_id')
            ->join('jelentkezos as jo', 'j.jelentkezo_id', '=', 'jo.id')
            ->where('sz.id', $szak_id)
            ->selectRaw('COUNT(*) as count')
            ->first();

        return response()->json($result ? ['count' => $result->count] : ['count' => 0]);
    }

    public function jelentkezokSzamaSzakraStat()
    {
        $result = DB::table('szaks as sz')
            ->join('jelentkezes as j', 'sz.id', '=', 'j.szak_id')
            ->join('jelentkezos as jo', 'j.jelentkezo_id', '=', 'jo.id')
            ->selectRaw('sz.elnevezes, COUNT(*) as ennyien ')
            ->groupBy('sz.elnevezes')
            ->get();
        return $result;
    }

    public function jelentkezokTagozatonkentSzakonkent()
    {
        $result = DB::table('szaks as sz')
            ->join('jelentkezes as j', 'sz.id', '=', 'j.szak_id')
            ->join('jelentkezos as jo', 'j.jelentkezo_id', '=', 'jo.id')
            ->selectRaw('sz.elnevezes, Count(*) as osszesen,   COUNT(CASE WHEN nappali = 1 THEN 1 END) AS nappali,
                            COUNT(CASE WHEN nappali = 0 THEN 1 END) AS esti')
            ->groupBy('sz.elnevezes')
            ->get();
        return $result;
    }
    public function jelentkezokTagozatonkent()
    {
        $result = DB::table('szaks as sz')
            ->join('jelentkezes as j', 'sz.id', '=', 'j.szak_id')
            ->join('jelentkezos as jo', 'j.jelentkezo_id', '=', 'jo.id')
            ->selectRaw('COUNT(CASE WHEN nappali = 1 THEN 1 END) AS nappali,
                            COUNT(CASE WHEN nappali = 0 THEN 1 END) AS esti')
            ->get();
        return $result;
    }
}
