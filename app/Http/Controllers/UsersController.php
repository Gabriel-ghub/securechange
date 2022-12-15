<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function peticionesFirmadas(Request $request)
    {
        $id = Auth::id();
        $usuario = User::findOrFail($id);
        $peticiones = $usuario->firmas;
        return $peticiones;
        //return view('peticiones.index', compact('peticiones'));
    }
}
