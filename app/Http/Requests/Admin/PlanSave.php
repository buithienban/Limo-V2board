<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PlanSave extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'content' => '',
            'group_id' => 'required',
            'transfer_enable' => 'required',
            'device_limit' => 'nullable|integer',
            'day_price' => 'nullable|integer',
            'week_price' => 'nullable|integer',
            'month_price' => 'nullable|integer',
            'quarter_price' => 'nullable|integer',
            'half_year_price' => 'nullable|integer',
            'year_price' => 'nullable|integer',
            'two_year_price' => 'nullable|integer',
            'three_year_price' => 'nullable|integer',
            'onetime_price' => 'nullable|integer',
            'reset_price' => 'nullable|integer',
            'reset_traffic_method' => 'nullable|integer|in:0,1,2,3,4,5',
            'capacity_limit' => 'nullable|integer',
            'speed_limit' => 'nullable|integer'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '套餐名称不能为空',
            'type.required' => '套餐类型不能为空',
            'type.in' => '套餐类型格式有误',
            'group_id.required' => '权限组不能为空',
            'transfer_enable.required' => '流量不能为空',
            'device_limit.integer' => '设备数限制格式有误',
            'day_price' => 'định dạng số tiền ngày không đúng',
            'week_price' => 'định dạng số tiền tuần không đúng',
            'month_price.integer' => 'định dạng số tiền tháng không đúng',
            'quarter_price.integer' => 'định dạng số tiền Quý không đúng',
            'half_year_price.integer' => 'định dạng số tiền nửa năm không đúng',
            'year_price.integer' => 'định dạng số tiền năm không đúng',
            'two_year_price.integer' => 'định dạng số tiền 2 năm không đúng',
            'three_year_price.integer' => 'định dạng số tiền 3 năm không đúng',
            'onetime_price.integer' => 'định dạng số tiền Vĩnh viễn không đúng',
            'reset_price.integer' => '流量重置包金额有误',
            'reset_traffic_method.integer' => '流量重置方式格式有误',
            'reset_traffic_method.in' => '流量重置方式格式有误',
            'capacity_limit.integer' => '容纳用户量限制格式有误',
            'speed_limit.integer' => '限速格式有误'
        ];
    }
}
