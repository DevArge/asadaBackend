<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Factura;
use App\DetalleFactura;
use DB;

class FacturasCuentasController extends Controller{

    // public function __construct(){
    //     $this->middleware('jwt.auth');
    //     $this->middleware('secretaria');
    // }

    public function getFacturas(Request $r){
        $total = DB::table('facturas')->count();
        $facturas = Factura::obtenerFacturas($r->desde, $r->cantidad, $r->columna, $r->orden);
        return response()->json(['ok'=> true, 'facturas' => $facturas, 'total' => $total], 200);
    }

    public function getFactura(Request $request, $id){
        $cuenta = Factura::find($id);
        if (!$cuenta) {
            return response()->json(['ok'=> false, 'message' => 'El ID: ' . $id . ' no existe'], 403);        
        }
        // $proyectos = Proyectos::all();
        $factura = Factura::with('productos')->findOrFail($id);
        return response()->json(['ok'=> true, 'factura' => $factura], 200);
    }

    public function buscarFacturas(Request $r, $termino = ''){
        $facturas = Factura::paginarFacturas($termino, $r->desde, $r->cantidad, $r->columna, $r->orden);
        $total = Factura::buscarFactura($termino)->count();
        return response()->json(['ok'=> true, 'cuentas' => $facturas, 'total' => $total], 200);
    }

    public function postFactura(Request $request){
        $this->validate($request, [
            'numero' => 'required|unique:facturas',
            'descripcion' => 'required|max:255',
            'idCuenta' => 'required',
            'fecha' => 'required',
            'descuento' => 'required|numeric|min:0',
            'productos.*.nombre' => 'required|max:255',
            'productos.*.precio' => 'required|numeric|min:1',
            'productos.*.cantidad' => 'required|integer|min:1'
        ]);
  
        $productos = collect($request->productos)->transform(function($product) {
            $product['total'] = $product['cantidad'] * $product['precio']; 
            return new DetalleFactura($product);
        });
        if($productos->isEmpty()) {
            return response()->json(['products_empty' => ['Uno o más productos son requeridos.']], 422);
        }
        $data = $request->except('productos');
        $data['sub_total'] = $productos->sum('total');
        $data['grand_total'] = $data['sub_total'] - $data['descuento'];
        $factura = Factura::create($data);
        $factura->productos()->saveMany($productos);
        return response()->json(['created' => true, 'id' => $factura->id ]);
    }

    public function putFactura(Request $request, $id){
        $this->validate($request, [
            'numero' => 'required',
            'descripcion' => 'required|max:255',
            'idCuenta' => 'required|max:255',
            'fecha' => 'required',
            'descuento' => 'required|numeric|min:0',
            'productos.*.nombre' => 'required|max:255',
            'productos.*.precio' => 'required|numeric|min:1',
            'productos.*.cantidad' => 'required|integer|min:1'
        ]);
        $factura = Factura::findOrFail($id);
        $productos = collect($request->productos)->transform(function($product) {
            $product['total'] = $product['cantidad'] * $product['precio'];
            return new DetalleFactura($product);
        });
        if($productos->isEmpty()) {
            return response()->json(['products_empty' => ['Uno o más productos son requeridos.']], 422);
        }
        $data = $request->except('productos');
        $data['sub_total'] = $productos->sum('total');
        $data['grand_total'] = $data['sub_total'] - $data['descuento'];
        $factura->update($data);
        DetalleFactura::where('factura_id', $factura->id)->delete();
        $factura->productos()->saveMany($productos);
        return response()->json([ 'updated' => true, 'id' => $factura->id ]);
    }

    public function deleteFactura($id){
        $factura = Factura::find($id);
        if (!$factura) {
            return response()->json(['ok'=> false, 'message' => 'El ID: ' . $id . ' no existe'], 403);        
        }
        DetalleFactura::where('factura_id', $factura->id)->delete();
        $factura->delete();
        return response()->json(['ok' => true, 'message' => 'Factura eliminada correctamente'], 201);
    }
}
