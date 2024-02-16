<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;

class ProductValidationService
{
    public function validationCreateProduct(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|min:3',
            'quantity' => 'required|numeric'
        ]);

        return $this->getValidationResponse($validator);
    }

    public function getValidationResponse($validator)
    {
        if ($validator->fails()) {
            $errors = $validator->errors()->all()[0];
            return [
                'status' => false,
                'message' => $errors,
                'data' => []
            ];
        }

        return [
            'status' => true,
            'message' => 'Validation successfull',
            'data' => []
        ];
    }
}