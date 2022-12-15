<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'show']]);
    }

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
        $validator = Validator::make(
            $request->all(),
            [
                'titulo' => 'required|max:255',
                'descripcion' => 'required',
                'destinatario' => 'required',
                'category_id' => 'required',
                //'file' => 'required',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'file' => 'required|mimes:png,jpg|max:4096',
            ]
        );
        if ($validator->fails()) {
            return response()->json(
                ['error' => $validator->errors()],
                401
            );
        }

        $imagen = $request->file('file');
        $nombre = time() . '-' . $imagen->getClientOriginalName();
        $ruta =  public_path() . '/img/posts';
        $imagen->move($ruta, $nombre);

        $input = $request->all();
        $category = Category::findOrFail($input['category_id']);
        $user = Auth::user(); //asociarlo al usuario authenticado
        $peticion = new Post($input);
        $peticion->user()->associate($user);
        $peticion->category()->associate($category);
        $peticion->firmantes = 0;
        $peticion->estado = 'pendiente';
        $peticion->save();

        $file = new File();
        $file->name= $nombre;
        $file->post_id = $peticion->id;
        $file->file_path= $ruta;
        $file->post()->associate($peticion);

        return response()->json(['message' => 'Esta es la peticion que acabas de guardar', 'data' => $peticion], 200);
    }

    public function firmar(Request $request, $id)
    {
        try {
            $peticion = Post::findOrFail($id);
            $user = Auth::user();
            $firmas = $peticion->firmas;
            foreach ($firmas as $firma) {
                if ($firma->id == $user->id) {
                    return response()->json(['message' => 'Ya has firmado esta petición'], 403);
                }
            }
            $user_id = [$user->id];
            $peticion->firmas()->attach($user_id);
            $peticion->firmantes = $peticion->firmantes + 1;
            $peticion->save();
        } catch (\Throwable $th) {
            return response()->json(['message' => 'La petición no se ha podido firmar'], 500);
        }
        return response()->json(['message' => 'Peticion firmada satisfactioriamente', 'peticion' => $peticion], 201);
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
