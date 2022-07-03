<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\TransactionResource;
use Illuminate\Support\Str;
use Auth;
use App\Models\Product;

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
        return response()->json([
                'status' => 'success',
                'message' => 'Transaction list',
                'data' => $transaction,
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
        if (Auth::user()->role != 'admin') {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|string|max:255',
                'amount' => 'required|numeric',
            ]);
    
            if ($validator->fails()) return sendError('Validation Error.', $validator->errors(), 422);
    
            $product = Product::where('id', $request->product_id)->get()->first();
            if ($product->quantity > 0 && $request->amount <= $product->quantity) {
                $harga = $product->price;
                $pajak = 10/100 * $harga;
                $biayaadmin = 5/100 * $harga + $pajak;
                $adminfee = $biayaadmin;
                $amount = $request->amount * $harga;
                $total = $biayaadmin + $amount + $pajak;
                $transaction = Transaction::create([
                    'uuid' => Str::uuid(),
                    'user_id' => Auth::user()->id,
                    'product_id' => $request->product_id,
                    'amount' =>$request->amount,
                    'tax' => $pajak,
                    'admin_fee' => $biayaadmin,
                    'total' => $total,
                ]);

                $product->update([
                    'quantity' => $product->quantity - $request->amount,
                ]);
        
                return sendResponse(new TransactionResource($transaction), 'Transaction created successfully');
            } else {
                return sendError('Product quantity is not enough', [], 422);
            }
            
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
        $transaction = Transaction::where('uuid', $uuid)->get()->first();
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
        $transaction = Transaction::where('uuid',$uuid)->get()->first();
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
        $transaction = Transaction::where('uuid' , '=' ,$uuid)->get()->first();
        if (is_null($transaction)) return sendError('Transaction not found.');
        $transaction->delete();
        return sendResponse(new TransactionResource($transaction), 'Transaction deleted successfully');
    }
}
