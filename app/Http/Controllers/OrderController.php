<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller {
    use HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index() {
        try {
            $user = Auth::user();

            if ($user->role === 1) {
                $orders = Order::all();
            } else if ($user->role === 2) {
                $orders = Order::where('user_id', $user->id)->get();
            }

            if ($orders->isEmpty()) {
                return $this->error(null, 'No record found', 404);
            }

            return $this->success($orders, 'Data retrieved successfully');
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        try {
            $user = Auth::user();

            $validator = Validator::make($request->all(), [
                'shipping_information_id' => 'required|integer',
                'payment_method_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                $validationErrors = $validator->errors()->toArray();
                $formattedErrors = [];
                foreach ($validationErrors as $field => $error) {
                    $formattedErrors[$field] = $error[0];
                }

                return $this->error(null, $formattedErrors, 422);
            }

            if ($user->carts->isEmpty()) {
                return $this->error(null, 'The cart must not be empty to place an order', 422);
            }

            $totalAmount = 0;
            foreach ($user->carts as $cartItem) {
                $totalAmount += $cartItem->quantity * $cartItem->product->price;
            }

            $order = Order::create([
                'user_id' => $user->id,
                'amount' => $totalAmount,
                'status' => $request->status,
                'shipping_information_id' => $request->shipping_information_id,
                'payment_method_id' => $request->payment_method_id,
            ]);

            foreach ($user->carts as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price
                ]);
                $cartItem->delete();
            }

            $order->load('paymentMethod');

            $response = [
                'amount' => $totalAmount,
                'status' => $order->status,
                'payment_method' => [
                    'name' => $order->paymentMethod->name,
                    'account_name' => $order->paymentMethod->account_name,
                    'account_number' => $order->paymentMethod->account_number,
                ],
            ];

            return $this->success($response, "Order placed successfully", 201);
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id) {
        try {
            $user = Auth::user();
            $order = Order::with('orderItems')->with('shippingInformation')->where('user_id', $user->id)->find($id);

            if (!$order) {
                return $this->error(null, 'No record found', 404);
            }

            return $this->success($order, 'Data retrieved successfully');
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id) {
        try {
            $order = Order::find($id);

            if (!$order) {
                return $this->error(null, 'Order not found', 404);
            }

            $order->update($request->only(['status']));

            return $this->success($order, "Order updated successfully", 200);
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id) {
        try {
            $order = Order::find($id);

            if (!$order) {
                return $this->error(null, 'Order not found', 404);
            }

            $order->delete();

            return $this->success($order, 'Order deleted successfully');
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        }
    }

    public function paymentDetail(int $id) {
        try {
            $user = Auth::user();
            $order = Order::with('paymentMethod')
                ->where('user_id', $user->id)
                ->find($id);

            if (!$order) {
                return $this->error(null, 'Order not found', 404);
            }

            $response = [
                'amount' => $order->amount,
                'payment_method' => $order->paymentMethod,
            ];

            return $this->success($response, 'Data retrieved successfully');
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        }
    }
}
