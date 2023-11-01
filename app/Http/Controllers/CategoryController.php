<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller {
    use HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index() {
        try {
            $categories = Category::all();

            if ($categories->isEmpty()) {
                return $this->error(null, 'No record found', 404);
            }

            return $this->success($categories, 'Data retrieved successfully');
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        };
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'string'
            ]);

            if ($validator->fails()) {
                $validationErrors = $validator->errors()->toArray();
                $formattedErrors = [];
                foreach ($validationErrors as $field => $error) {
                    $formattedErrors[$field] = $error[0];
                }

                return $this->error(null, $formattedErrors, 422);
            }

            $category = Category::create($request->only(['name', 'description']));

            if ($category) {
                return $this->success($category, "Category added successfully", 201);
            }
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id) {
        try {
            $category = Category::find($id);

            if (!$category) {
                return $this->error(null, 'No record found', 404);
            }

            return $this->success($category, 'Data retrieved successfully');
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id) {
        try {
            $category = Category::find($id);

            if (!$category) {
                return $this->error(null, 'Category not found', 404);
            }

            $category->update($request->only(['name', 'description']));

            return $this->success($category, "Category updated successfully", 201);
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id) {
        try {
            $category = Category::find($id);

            if (!$category) {
                return $this->error(null, 'Category not found', 404);
            }

            $category->delete();

            return $this->success($category, 'Category deleted successfully');
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        }
    }
}
