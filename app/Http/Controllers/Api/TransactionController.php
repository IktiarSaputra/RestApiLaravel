<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Auth;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transaction = Transaction::orderBy('created_at', 'DESC')->paginate(3);
        return response ()->json([
            "success" => true,
            "message" => "Transaction list",
            "data" => $transaction
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = Product::find($request->product_id);
        $harga = $product->price;
        $pajak = 10/100 * $harga;
        $biayaadmin = 5/100 * $harga + $pajak;
        $total = $harga + $biayaadmin;
        $transaction = Transaction::create([
            'uuid' => Str::uuid(),
            'user_id' => Auth::user()->id,
            'product_id' => $request->product_id,
            'amount' =>$request->amount,
            'tax' => $pajak,
            'admin_fee' => $biayaadmin,
            'total' => $total,
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        $transaction = Transaction::find($uuid);
        return response()->json([
            "success" => true,
            "message" => "Transaction found successfully",
            "data" => $transaction
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {
        $transaction = Transaction::find($uuid);
        return response()->json([
            "success" => true,
            "message" => "Transaction found successfully",
            "data" => $transaction
        ]);

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
        $transaction = Transaction::find($uuid);
        $transaction->update($request->all());
        response()->json([
            "success" => true,
            "message" => "Transaction updated successfully",
            "data" => $transaction
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        $transaction = Transaction::find($uuid);
        $transaction->delete();
        return response()->json([
            "success" => true,
            "message" => "Transaction deleted successfully",
            "data" => $transaction
        ]);
    }
}
