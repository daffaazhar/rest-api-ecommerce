<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller {
    use HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index() {
        try {
            $items = Auth::user()->carts;

            if ($items->isEmpty()) {
                return $this->error(null, 'No record found', 404);
            }

            return $this->success($items, 'Data retrieved successfully');
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
                'product_id' => 'required|integer',
                'quantity' => 'required|integer',
            ]);

            if ($validator->fails()) {
                $validationErrors = $validator->errors()->toArray();
                $formattedErrors = [];
                foreach ($validationErrors as $field => $error) {
                    $formattedErrors[$field] = $error[0];
                }

                return $this->error(null, $formattedErrors, 422);
            }

            $existingItem = $user->carts
                ->where('product_id', $request->product_id)
                ->first();

            if ($existingItem) {
                $existingItem->quantity += $request->quantity;
                $existingItem->save();
                $item = $existingItem;
            } else {
                $item = Cart::create([
                    'user_id' => $user->id,
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity,
                ]);
            }

            if ($item) {
                return $this->success($item, "Item added successfully", 201);
            }
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id) {
        try {
            $item = Auth::user()->carts->find($id);

            if (!$item) {
                return $this->error(null, 'Item not found', 404);
            }

            $item->update($request->only(['quantity']));

            return $this->success($item, "Quantity updated successfully", 201);
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id) {
        try {
            $item = Auth::user()->carts->find($id);

            if (!$item) {
                return $this->error(null, 'Item not found', 404);
            }

            $item->delete();

            return $this->success($item, 'Item deleted successfully');
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        }
    }
}
