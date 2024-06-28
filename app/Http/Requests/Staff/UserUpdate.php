<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdate extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            
            'staff_title' => '',
            'staff_mota' => '',
            'staff_zalo' => '',
            'staff_telegram' => '',
            'staff_logo' => ''
        ];
    }

    public function messages()
    {
        return [
            'staff_zalo.url' => 'URL Zalo phải có https:// hoặc http://',
            'staff_logo.url' => 'URL Logo phải có https:// hoặc http://',
            'staff_telegram.url' => 'URL Telegram phải có https:// hoặc http://'
        ];
    }
}
