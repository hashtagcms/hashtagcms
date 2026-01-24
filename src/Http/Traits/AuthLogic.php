<?php

namespace HashtagCms\Http\Traits;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use HashtagCms\User;

trait AuthLogic
{
    /**
     * Get Register Validation Rules
     * @return array
     */
    public function getRegisterRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed', // Web usually expects confirmed
        ];
    }

    /**
     * Get API Register Rules (password confirmation might be optional or handled differently)
     * @return array
     */
    public function getApiRegisterRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ];
    }

    /**
     * Validate User Data
     * @param array $data
     * @param array $rules
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validateUser(array $data, array $rules): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance
     * @param array $data
     * @return User
     */
    public function createUser(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'user_type' => $data['user_type'] ?? 'Visitor',
        ]);
    }
}
