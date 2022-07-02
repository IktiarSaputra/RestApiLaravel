<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Auth;
use Exception;
use Illuminate\Support\Str;
use App\Http\Resources\ProductResource;
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
        return response()->json(
            [
                'status' => 'success',
                'message' => 'Product list',
                'data' => $products,
            ]
        );
        // return sendResponse(ProductResource::collection($products), 'Product list');
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
        if (Auth::user()->role == 'admin') {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'price' => 'required|numeric',
                'quantity' => 'required|numeric',
            ]);
    
            if ($validator->fails()) return sendError('Validation Error.', $validator->errors(), 422);
    
            try {
                $products = Product::create([
                    'uuid' => Str::uuid(),
                    'name' => $request->name,
                    'type' => $request->type,
                    'price' => $request->price,
                    'quantity' => $request->quantity,
                ]);
                $success = new ProductResource($products);
                $message = 'Product created successfully';
            } catch (Exception $e) {
                $success = [];
                $message = 'Product creation failed';
            }
    
            return sendResponse($success, $message);
        } else {
            return sendError('unauthorization', [], 401);
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        $products = Product::where('uuid' , '=' ,$uuid)->get()->first();
        if (is_null($products)) return sendError('Post not found.');
        return sendResponse(new ProductResource($products), 'Product found successfully');
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
            $products = Product::find($uuid);
            if (is_null($products)) return sendError('Post not found.');
            return sendResponse(new ProductResource($products), 'Product found successfully');
        } else {
            return sendError('unauthorization', [], 401);
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
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'price' => 'required|numeric',
                'quantity' => 'required|numeric',
            ]);
    
            if ($validator->fails()) return sendError('Validation Error.', $validator->errors(), 422);

            $product = Product::where('uuid' , '=' ,$uuid)->get()->first();

            if (is_null($product)) return sendError('Product not found.');

            $product->update($request->all());
            return response()->json([
                "success" => true,
                "message" => "Product updated successfully",
                "data" => $product
            ]);
        } else {
            return sendError('unauthorization', [], 401);
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
            $product = Product::where('uuid' , '=' ,$uuid)->get()->first();
            if (is_null($product)) return sendError('Product not found.');
            $product->delete();
            return response()->json([
                "success" => true,
                "message" => "Product deleted successfully",
                "data" => $product
            ]);
        } else {
            return sendError('unauthorization', [], 401);
        }
    }
}
