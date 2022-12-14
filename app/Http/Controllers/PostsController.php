<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function list(Request $request)
    {
        $peticiones = Post::jsonPaginate();
        return response()->json(['message' => 'Estas son todas las peticiones paginadas', 'data' => $peticiones], 200);
    }
     
    public function index(Request $request)
    {
        $peticiones = Post::all();
        return response()->json(['message' => 'Estas son todas las peticiones', 'data' => $peticiones], 200);
    }
    public function listMine(Request $request)
    {
        // parent::index()
        //$user = Auth::user();
        $id = 1;
        $peticiones = Post::all()->where('user_id', $id);
        return response()->json(['message' => 'Estas son las peticiones del usuario ingresado', 'data' => $peticiones], 200);
    }
    public function show(Request $request, $id)
    {
        $peticion = Post::findOrFail($id);
        return $peticion;
        return response()->json(['message' => 'Esta es la peticion buscada', 'data' => $peticion], 200);
    }
    public function update(Request $request, $id)
    {
        $peticion = Post::findOrFail($id);
        $peticion->update($request->all());
        return response()->json(['message' => 'Esta es la peticion modificada', 'data' => $peticion], 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'titulo' => 'required|max:255',
            'descripcion' => 'required',
            'destinatario' => 'required',
            'file' => 'required',
        ]);
        $input = $request->all();
        $category = Category::findOrFail($input['category']);
        //$user = Auth::user(); //asociarlo al usuario authenticado
        $peticion = new Post($input);
        //$peticion->user()->associate($user);
        $peticion->category()->associate($category);
        $peticion->firmantes = 0;
        $peticion->estado = 'pendiente';
        $peticion->save();
        return response()->json(['message' => 'Esta es la peticion que acabas de guardar', 'data' => $peticion], 200);
    }

    public function firmar(Request $request, $id)
    {
        $peticion = Post::findOrFail($id);
        //$user = Auth::user();
        $user = 1;
        $user_id = [$user];
        //$user_id = [$user->id];
        $peticion->firmas()->attach($user_id);
        return response()->json(['message' => 'Estas son las firmas de la peticion', 'data' => $peticion->firmas()], 200);
    }
    public function cambiarEstado(Request $request, $id)
    {
        $peticion = Post::findOrFail($id);
        $peticion->estado = 'aceptada';
        $peticion->save();
        return response()->json(['message' => 'Esta es la peticion que cambio de estado', 'data' => $peticion], 200);
    }
    public function destroy(Request $request)
    {
        $peticion = Post::findOrFail($request->postid);
        $peticion->delete();
        return response()->json(['message' => 'Esta es la peticion borrada', 'data' => $peticion], 200);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
}
