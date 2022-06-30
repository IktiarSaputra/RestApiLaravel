<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Auth;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::orderBy('created_at', 'DESC')->paginate(3);
        return response ()->json($products, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $products = Product::create([
            'uuid' => Str::uuid(),
            'name' => $request->name,
            'type' => $request->type,
            'price' => $request->price,
            'quantity' => $request->quantity,
        ]);
        return response ()->json([
            "success" => true,
            "message" => "Product created successfully",
            "data" => $products
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        if (Auth::user()->role == 'admin') {
            $products = Product::where($uuid)->first();
            return response ()->json([
                "success" => true,
                "message" => "Product found successfully",
                "data" => $products
            ]);
        } else {
            return response ()->json([
                "success" => false,
                "message" => "You are not authorized to access this resource"
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {
        if (Auth::user()->role == 'admin') {
            $product = Product::find($uuid);
            return response()->json([
                "success" => true,
                "message" => "Product found successfully",
                "data" => $product
            ]);
        } else {
            return response ()->json([
                "success" => false,
                "message" => "You are not authorized to access this resource"
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        if (Auth::user()->role == 'admin') {
            $product = Product::find($uuid);
            $product->update($request->all());
            return response()->json([
                "success" => true,
                "message" => "Product updated successfully",
                "data" => $product
            ]);
        } else {
            return response ()->json([
                "success" => false,
                "message" => "You are not authorized to access this resource"
            ]);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        if (Auth::user()->role == 'admin') {
            $product = Product::find($uuid);
            $product->delete();
            return response()->json([
                "success" => true,
                "message" => "Product deleted successfully",
                "data" => $product
            ]);
        } else {
            return response ()->json([
                "success" => false,
                "message" => "You are not authorized to access this resource"
            ]);
        }
    }
}
