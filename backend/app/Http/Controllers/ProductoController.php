<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    /**
     * Mostrar todos los productos disponibles (Para la tienda/mapa)
     */
    public function index() {
        $productos = Producto::all();

        return response()->json($productos);
    }

    public function productosPorUsuario(Request $request)
    {
        $user = $request->user();

        $productos = Producto::where('id_usuario', $user->id)
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json($productos);
    }

    /**
     * Crear un nuevo producto (Para el agricultor)
     */
    public function store(Request $request) {

        $validado = $request->validate([
            'id_categoria' => 'required|exists:categorias,id',
            'nombre_producto' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock_total' => 'required|integer|min:1',
            'imagen' => 'nullable|string',
        ]);

        Producto::create($validado);

        return response()->json([
            'message' => 'Producto publicado con éxito',
            'producto' => $validado
        ], 201);
    } // Aquí termina la función store correctamente

    /**
     * Ver detalle de un producto específico
     */
    public function show($id) {
        $producto = Producto::with('categoria')->findOrFail($id);
        return response()->json($producto);
    }

    /**
     * Actualizar datos del producto
     */
    public function update(Request $request, $id) {
        $producto = Producto::findOrFail($id);

        $request->validate([
            'precio' => 'numeric|min:0',
            'stock_total' => 'integer|min:0',
        ]);

        $producto->update($request->all());

        return response()->json([
            'message' => 'Producto actualizado',
            'producto' => $producto
        ]);
    }
}