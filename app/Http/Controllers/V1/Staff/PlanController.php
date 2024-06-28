<?php

namespace App\Http\Controllers\V1\Staff;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanController extends Controller
{
    public function fetch(Request $request)
    {
        $staffPlans = DB::table('v2_user')->pluck('staff_plan');
        $staffPlansArray = [];

        
        foreach ($staffPlans as $staffPlan) {
            $staffPlansArray = array_merge($staffPlansArray, explode(',', $staffPlan));
        }

        $counts = User::select(
            DB::raw("plan_id"),
            DB::raw("count(*) as count")
        )
            ->whereIn('plan_id', $staffPlansArray) 
            ->where('plan_id', '!=', NULL)
            ->where(function ($query) {
                $query->where('expired_at', '>=', time())
                    ->orWhere('expired_at', NULL);
            })
            ->groupBy("plan_id")
            ->get();

        $plans = Plan::whereIn('id', $staffPlansArray) 
                    ->orderBy('sort', 'ASC')
                    ->get();

        foreach ($plans as $k => $v) {
            $plans[$k]->count = 0;
            foreach ($counts as $kk => $vv) {
                if ($plans[$k]->id === $counts[$kk]->plan_id) $plans[$k]->count = $counts[$kk]->count;
            }
        }

        return response([
            'data' => $plans
        ]);
    }
}
