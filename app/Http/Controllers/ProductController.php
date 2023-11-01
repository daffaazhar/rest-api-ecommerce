<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller {
    use HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index() {
        try {
            $products = Product::all();

            if ($products->isEmpty()) {
                return $this->error(null, 'No record found', 404);
            }

            return $this->success($products, 'Data retrieved successfully');
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'category_id' => 'required|integer',
                'name' => 'required|string|max:255',
                'description' => 'string',
                'price' => 'required|numeric',
                'stock' => 'required|integer',
                'image' => 'string'
            ]);

            if ($validator->fails()) {
                $validationErrors = $validator->errors()->toArray();
                $formattedErrors = [];
                foreach ($validationErrors as $field => $error) {
                    $formattedErrors[$field] = $error[0];
                }

                return $this->error(null, $formattedErrors, 422);
            }

            $product = Product::create([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock,
                'image' => $request->image
            ]);

            if ($product) {
                return $this->success($product, "Product added successfully", 201);
            }
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {
        try {
            $product = Product::find($id);

            if (!$product) {
                return $this->error(null, 'No record found', 404);
            }

            return $this->success($product, 'Data retrieved successfully');
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id) {
        try {
            $product = Product::find($id);

            if (!$product) {
                return $this->error(null, 'Product not found', 404);
            }

            $product->update($request->only([
                'category_id', 'name', 'description', 'price', 'stock', 'image'
            ]));

            return $this->success($product, "Product updated successfully", 200);
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id) {
        try {
            $product = Product::find($id);

            if (!$product) {
                return $this->error(null, 'Product not found', 404);
            }

            $product->delete();

            return $this->success($product, 'Product deleted successfully');
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        }
    }
}
