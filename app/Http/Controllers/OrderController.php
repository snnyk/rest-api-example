<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Http\Resources\OrderResource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orders = Order::latest()->paginate(30);

        return [
            'orders' => OrderResource::collection($orders)
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderStoreRequest $request)
    {
        $calculated = $this->calculator($request->products);

        $data = [
            'customerId' => $request->customerId,
            'items' => $calculated['items'],
            'total' => $calculated['total']
        ];

        try{

            //start transaction for safe insert
            DB::beginTransaction();
            //create order
            $order = Order::create($data);
            //set stocks
            Product::setStock($request->products);
            //update customer revenue
            Customer::whereId($request->customerId)->increment('revenue' , $order->total);

            //commit queries
            DB::commit();

            return [
                'status' => true,
                'mesage' => 'Order created',
                'order_id' => $order->id
            ];

        }catch(Exception $e){
            //rolback data if an error accorred
            DB::rollBack();

            return response([
                'status' => false,
                'message' => $e->getMessage()
            ], 403);
        }

    }

    private function calculator($products)
    {
        $items = [];
        $total = 0;

        foreach($products as $product){

            $productModel = Product::whereId($product['productId'])->first();

            $items[] = [
                'productId' => $product['productId'],
                'quantity' => $product['quantity'],
                'unitPrice' => $unitPrice = $productModel->price,
                'categoryId' => $productModel->category,
                'total' => $productTotal = ( $product['quantity'] * $unitPrice )
            ];

            $total += $productTotal;
        }

        return [
            'items' => json_encode($items),
            'total' => $total
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            Order::findOrFail($id)->delete();

            return [
                'status' => true,
                'mesage' => 'Order deleted',
                'order_id' => $id
            ];
        }catch(Exception $e){
            return response([
                'status' => false,
                'message' => $e->getMessage()
            ], 403);
        }
    }
}
