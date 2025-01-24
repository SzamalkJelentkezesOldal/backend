<?php

namespace App\Http\Controllers;

use App\Models\Ugyintezo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UgyintezoController extends Controller
{
    //
    public function postUgyintezo(Request $request)
    {
        $ellenorzottAdatok = $request->validate([
            'nev' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'jelszo' => 'required|string|min:6',
            'jelszoMegerosites' => 'required|same:jelszo',
            'master' => 'boolean',
        ]);

   
        $ujUgyintezo = User::create([
            'name' => $ellenorzottAdatok['nev'],
            'email' => $ellenorzottAdatok['email'],
            'password' => Hash::make($ellenorzottAdatok['jelszo']),
            'role' => $ellenorzottAdatok['master'] ? 2 : 1, 
        ]);

        return response()->json($ujUgyintezo);
    }

    public function getUgyintezok()
    {
        $response = User::where('role', '>', 0)->get(); 
        return response()->json($response);
    }

    public function ugyintezoDelete(string $id)
    {
        Ugyintezo::find($id)->delete();
    }

    public function ugyintezoPatch(Request $request, string $id)
    {
        $record = Ugyintezo::find($id);
        $record->fill($request->all());
        $record->save();
    }
}
