<?php
namespace App\Http\Routes\V2;

use Illuminate\Contracts\Routing\Registrar;

class StaffRoute
{
    public function map(Registrar $router)
    {
        $router->group([
            'prefix' => config('v2board.staff_path'), // Đây là tiền tố cho các route của staff, bạn có thể thay đổi nó theo ý muốn
            'middleware' => ['staff', 'log'], // Đảm bảo bạn đã định nghĩa middleware 'staff' nếu cần kiểm tra quyền truy cập
        ], function ($router) {
            // Định nghĩa các route cụ thể cho staff ở đây
            $router->get('/dashboard', 'V2\\Staff\\DashboardController@index');
            // Thêm các route khác mà staff cần truy cập
        });
    }
}
