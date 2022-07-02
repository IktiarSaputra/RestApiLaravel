<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\TransactionResource;
use Illuminate\Support\Str;
use Auth;

class TransactionController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transaction = Transaction::orderBy('created_at', 'DESC')->paginate(3);
        return sendResponse(TransactionResource::collection($transaction), 'Transaction list');
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
        $validator = Validator::make($request->all(), [
            'uuid' => 'required|string|max:255',
            'user_id' => 'required|string|max:255',
            'product_id' => 'required|string|max:255',
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) return sendError('Validation Error.', $validator->errors(), 422);

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

        return sendResponse(new TransactionResource($transaction), 'Transaction created successfully');

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
        return sendResponse(new TransactionResource($transaction), 'Transaction found');
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
        return sendResponse(new TransactionResource($transaction), 'Transaction found');

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
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string|max:255',
            'product_id' => 'required|string|max:255',
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) return sendError('Validation Error.', $validator->errors(), 422);

        $transaction = Transaction::where('uuid', '=' ,$uuid)->get()->first();
        $transaction->update($request->all());
        return sendResponse(new TransactionResource($transaction), 'Transaction updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Responseo
     */
    public function destroy($uuid)
    {
        $transaction = Transaction::find($uuid);
        $transaction->delete();
        return sendResponse(new TransactionResource($transaction), 'Transaction deleted successfully');
    }
}
