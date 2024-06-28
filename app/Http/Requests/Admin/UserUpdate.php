<?php

namespace App\Http\Requests\Admin;

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
            'email' => 'required|email:strict',
            'password' => 'nullable|min:8',
            'transfer_enable' => 'numeric',
            'device_limit' => 'nullable|integer',
            'network_settings' => '',
            'dname_sni' => '',
            'expired_at' => 'nullable|integer',
            'banned' => 'required|in:0,1',
            'plan_id' => 'nullable|integer',
            'commission_rate' => 'nullable|integer|min:0|max:100',
            'discount' => 'nullable|integer|min:0|max:100',
            'is_admin' => 'required|in:0,1',
            'is_staff' => 'required|in:0,1',
            'staff_url' => '',
            'staff_title' => '',
            'staff_mota' => '',
            'staff_logo' => '',
            'staff_plan' => '',
            'u' => 'integer',
            'd' => 'integer',
            'balance' => 'integer',
            'commission_type' => 'integer',
            'commission_balance' => 'integer',
            'remarks' => 'nullable',
            'speed_limit' => 'nullable|integer'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => '邮箱不能为空',
            'email.email' => '邮箱格式不正确',
            'transfer_enable.numeric' => '流量格式不正确',
            'device_limit.integer' => '设备数限制格式不正确',
            'expired_at.integer' => '到期时间格式不正确',
            'banned.required' => '是否封禁不能为空',
            'banned.in' => '是否封禁格式不正确',
            'is_admin.required' => '是否管理员不能为空',
            'is_admin.in' => '是否管理员格式不正确',
            'is_staff.required' => '是否员工不能为空',
            'is_staff.in' => '是否员工格式不正确',
            'staff_url.required' => 'Url nhân viên không được bỏ trống',
            'plan_id.integer' => '订阅计划格式不正确',
            'commission_rate.integer' => '推荐返利比例格式不正确',
            'commission_rate.nullable' => '推荐返利比例格式不正确',
            'commission_rate.min' => '推荐返利比例最小为0',
            'commission_rate.max' => '推荐返利比例最大为100',
            'discount.integer' => '专属折扣比例格式不正确',
            'discount.nullable' => '专属折扣比例格式不正确',
            'discount.min' => '专属折扣比例最小为0',
            'discount.max' => '专属折扣比例最大为100',
            'u.integer' => '上行流量格式不正确',
            'd.integer' => '下行流量格式不正确',
            'balance.integer' => '余额格式不正确',
            'commission_balance.integer' => '佣金格式不正确',
            'password.min' => '密码长度最小8位',
            'speed_limit.integer' => '限速格式不正确'
        ];
    }
}
