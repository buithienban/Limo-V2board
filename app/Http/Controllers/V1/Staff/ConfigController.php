<?php

namespace App\Http\Controllers\V1\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\UserUpdate;
use App\Jobs\SendEmailJob;
use App\Services\TelegramService;
use App\Utils\Dict;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;


class ConfigController extends Controller
{

    public function fetch(Request $request)
    {
        
        $user = User::where('id', $request->user['id'])
            ->select([
                'staff_title',
                'staff_mota',
                'staff_zalo',
                'staff_telegram',
                'staff_logo'
            ])
            ->first();
        if (!$user) {
            abort(500, __('The user does not exist'));
        }
        $user['commission_first_time_enable'] = config('v2board.commission_first_time_enable', 0);
        return response([
            'data' => [
                'site' => $user
            ]
        ]);
    }

    public function save(UserUpdate $request)
    {
        

        $data = $request->only([
            'staff_title',
            'staff_mota',
            'staff_zalo',
            'staff_telegram',
            'staff_logo'
        ]);

        $userData = $request->input('user');

        if ($userData !== null && isset($userData['id'])) {
            $user = User::find($userData['id']);
            if ($user === null) {
                // Handle the case where the user does not exist in the database
                return response()->json(['error' => 'The user does not exist'], 404);
            }
        }

        

        // Cập nhật các trường
        $user->staff_title = $data['staff_title'];
        $user->staff_mota = $data['staff_mota'];
        $user->staff_zalo = $data['staff_zalo'];
        $user->staff_telegram = $data['staff_telegram'];
        $user->staff_logo = $data['staff_logo'];

        

        // Lưu thay đổi
        if (!$user->save()) {
            
            abort(500, __('Failed to save user'));
        }

        

        return response([
            'data' => true
        ]);
    }
}
