<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
<<<<<<< HEAD
  
    public function index()
    {
        $productos = Producto::with('categoria')
            ->where('estado', 'disponible')
            ->where('stock', '>', 0)
            ->get();
=======
    /**
     * Mostrar todos los productos disponibles (Para la tienda/mapa)
     */
    public function index() {
        $productos = Producto::all();
>>>>>>> d364153867141342655ad70ab2c27cf1d9d6a264

        return response()->json($productos);
    }

<<<<<<< HEAD
    public function store(Request $request)
    {
        $request->validate([
            'id_categoria'    => 'required|exists:categorias,id',
            'nombre_producto' => 'required|string|max:255',
            'descripcion'     => 'nullable|string',
            'precio'          => 'required|numeric|min:0',
            'stock'     => 'required|integer|min:1',
            'imagen'          => 'nullable|string',
        ]);

        $producto = Producto::create([
            'id_categoria'    => $request->id_categoria,
            'nombre_producto' => $request->nombre_producto,
            'descripcion'     => $request->descripcion,
            'precio'          => $request->precio,
            'stock'     => $request->stock,
            'imagen'          => $request->imagen,
            'estado'          => 'disponible',
        ]);
=======
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
            'id_puntoentrega' => 'required|exists:puntos_entrega,id', 
            'nombre_producto' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock_total' => 'required|integer|min:1',
            'imagen' => 'nullable|image|max:2048',
        ]);

        $user = $request->user();

        $validado['id_usuario'] = $user->id;
        $validado['estado'] = $validado['estado'] ?? 'disponible';

        if ($request->hasFile('imagen')) {
        $validado['imagen'] = $request->file('imagen')->store('productos', 'public');
        } else {
            $validado['imagen'] = 'productos/default.png';
        }

        Producto::create($validado);
>>>>>>> d364153867141342655ad70ab2c27cf1d9d6a264

        return response()->json([
            'message' => 'Producto publicado con éxito',
            'producto' => $validado
        ], 201);
    } // Aquí termina la función store correctamente

<<<<<<< HEAD
    
    public function show($id)
    {
=======
    /**
     * Ver detalle de un producto específico
     */
    public function show($id) {
>>>>>>> d364153867141342655ad70ab2c27cf1d9d6a264
        $producto = Producto::with('categoria')->findOrFail($id);
        return response()->json($producto);
    }

<<<<<<< HEAD
   
    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $request->validate([
            'precio'      => 'numeric|min:0',
            'stock' => 'integer|min:0',
=======
    /**
     * Actualizar datos del producto
     */
    public function update(Request $request, $id) {
        $producto = Producto::findOrFail($id);

        $request->validate([
            'precio' => 'numeric|min:0',
            'stock_total' => 'integer|min:0',
>>>>>>> d364153867141342655ad70ab2c27cf1d9d6a264
        ]);

        $producto->update($request->all());

        return response()->json([
            'message' => 'Producto actualizado',
            'producto' => $producto
        ]);
    }
}