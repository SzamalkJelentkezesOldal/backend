<?php

namespace App\Http\Controllers;

use App\Models\Ugyintezo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UgyintezoController extends Controller
{
    //
    public function postUgyintezo(Request $request){
        $ellenorzottAdatok = $request->validate([
            'nev' => 'required|string|max:255',
            'email' => 'required|email|unique:ugyintezos,email',
            'jelszo' => 'required|string|min:6',
            'master' => 'boolean',
        ]);

        $ujUgyintezo = Ugyintezo::create([
            'nev' => $ellenorzottAdatok['nev'],
            'email' => $ellenorzottAdatok['email'],
            'jelszo' => $ellenorzottAdatok['jelszo'], 
            'master' => $ellenorzottAdatok['master'] ?? false,
        ]);

        return response()->json($ujUgyintezo);
    }
}
