<?php

namespace App\Http\Controllers;

use App\Models\Ugyintezo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UgyintezoController extends Controller
{
    //
    public function postUgyintezo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nev' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'jelszo' => 'required|string|min:8',
            'jelszoMegerosites' => 'required|string|same:jelszo',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->nev,
            'email' => $request->email,
            'password' => Hash::make($request->jelszo),
            'role' => 1, // Assign role 1 for Ugyintezo
        ]);

        return response()->json($user, 201);
    }

    public function getUgyintezok()
    {
        $response = User::where('role', '>', 0)->get(); 
        return response()->json($response);
    }

    public function ugyintezoDelete(string $id)
    {
        User::find($id)->delete();
    }

    public function ugyintezoPatch(Request $request, string $id)
    {
        $record = User::find($id);
        $record->fill($request->all());
        $record->save();
    }
}
