<?php

namespace App\Http\Controllers;

use App\Models\ShippingInformation;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ShippingInformationController extends Controller {
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index() {
        try {
            $shippingInfos = Auth::user()->shippingInformations;

            if ($shippingInfos->isEmpty()) {
                return $this->error(null, 'No record found', 404);
            }

            return $this->success($shippingInfos, 'Data retrieved successfully');
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
                'receiver_name' => 'required|string',
                'receiver_number_phone' => 'required|string|min:10',
                'street' => 'string',
                'village' => 'required|string',
                'district' => 'required|string',
                'regency' => 'required|string',
                'province' => 'required|string',
                'postal_code' => 'required|string'
            ]);

            if ($validator->fails()) {
                $validationErrors = $validator->errors()->toArray();
                $formattedErrors = [];
                foreach ($validationErrors as $field => $error) {
                    $formattedErrors[$field] = $error[0];
                }

                return $this->error(null, $formattedErrors, 422);
            }

            $shippingInfo = ShippingInformation::create([
                'user_id' => Auth::user()->id,
                'receiver_name' => $request->receiver_name,
                'receiver_number_phone' => $request->receiver_number_phone,
                'street' => $request->street,
                'village' => $request->village,
                'district' => $request->district,
                'regency' => $request->regency,
                'province' => $request->province,
                'postal_code' => $request->postal_code,
            ]);

            if ($shippingInfo) {
                return $this->success($shippingInfo, "Shipping information added successfully", 201);
            }
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id) {
        try {
            $shippingInfo = Auth::user()->shippingInformations->find($id);

            if (!$shippingInfo) {
                return $this->error(null, 'Shipping information', 404);
            }

            $shippingInfo->update($request->only([
                'receiver_name', 'receiver_number_phone', 'street', 'village', 'district', 'regency', 'province', 'postal_code',
            ]));

            return $this->success($shippingInfo, "Shipping information updated successfully", 201);
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id) {
        try {
            $shippingInfo = Auth::user()->shippingInformations->find($id);

            if (!$shippingInfo) {
                return $this->error(null, 'Shipping information not found', 404);
            }

            $shippingInfo->delete();

            return $this->success($shippingInfo, 'Shipping information deleted successfully');
        } catch (Exception $e) {
            return $this->error(null, $e, 500);
        }
    }
}
