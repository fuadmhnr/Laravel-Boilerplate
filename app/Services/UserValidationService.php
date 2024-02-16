<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;

class UserValidationService
{
    public function validationSignupRequest(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|min:2|max:45',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|max:45',
        ]);

        return $this->getValidationResponse($validator);
    }

    public function validateLoginRequest(array $data)
    {
        $validator = Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required|min:3|max:45',
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
