<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserChangeSNI extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'dname_sni' => 'required|max:100',// Add more validation rules as needed
        ];
    }
    public function messages()
    {
        return [
            'dname_sni.required' => 'SNI không được bỏ trống',
            'dname_sni.string' => 'Tên SNI phải là 1 chuỗi',
            // Add more custom messages as needed
        ];
    }
}
?>