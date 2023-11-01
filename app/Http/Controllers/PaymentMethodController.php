<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentMethodController extends Controller {
    use HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index() {
        $paymentMethods = PaymentMethod::all();

        if ($paymentMethods->isEmpty()) {
            return $this->error(null, 'No record found', 404);
        }

        return $this->success($paymentMethods, 'Data retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'account_name' => 'required|string',
                'account_number' => 'required|string',
            ]);

            if ($validator->fails()) {
                $validationErrors = $validator->errors()->toArray();
                $formattedErrors = [];
                foreach ($validationErrors as $field => $error) {
                    $formattedErrors[$field] = $error[0];
                }

                return $this->error(null, $formattedErrors, 422);
            }

            $paymentMethod = PaymentMethod::create([
                'name' => $request->name,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
            ]);

            if ($paymentMethod) {
                return $this->success($paymentMethod, "Payment method added successfully", 201);
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
            $paymentMethod = PaymentMethod::find($id);

            if (!$paymentMethod) {
                return $this->error(null, 'No record found', 404);
            }
            return $this->success($paymentMethod, 'Data retrieved successfully');
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id) {
        try {
            $paymentMethod = PaymentMethod::find($id);

            if (!$paymentMethod) {
                return $this->error(null, 'Payment method not found', 404);
            }

            $paymentMethod->update($request->only([
                'name', 'account_name', 'account_number'
            ]));

            return $this->success($paymentMethod, "Payment method updated successfully", 200);
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id) {
        try {
            $paymentMethod = PaymentMethod::find($id);

            if (!$paymentMethod) {
                return $this->error(null, 'Payment method not found', 404);
            }

            $paymentMethod->delete();

            return $this->success($paymentMethod, 'Payment method deleted successfully');
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        }
    }
}
