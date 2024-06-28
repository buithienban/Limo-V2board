<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserChangeAvatar extends FormRequest
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
            'avatar_url_new' => 'required|url|max:255',// Add more validation rules as needed
        ];
    }
    public function messages()
    {
        return [
            'avatar_url_new.required' => 'Avatar url không được bỏ trống',
            'avatar_url_new.url' => 'định dang không đúng',
            'avatar_url_new.max' => 'link ảnh quá dài max 255 kí tự',
            
            // Add more custom messages as needed
        ];
    }
}
?>