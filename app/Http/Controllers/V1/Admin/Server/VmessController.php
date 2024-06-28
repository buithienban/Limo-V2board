<?php

namespace App\Http\Controllers\V1\Admin\Server;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServerVmessSave;
use App\Http\Requests\Admin\ServerVmessUpdate;
use App\Models\ServerVmess;
use Illuminate\Http\Request;

class VmessController extends Controller
{
    public function save(ServerVmessSave $request)
    {
        $params = $request->validated();

        if ($request->input('id')) {
            $server = ServerVmess::find($request->input('id'));
            if (!$server) {
                abort(500, 'Máy chủ không tồn tại');
            }
            try {
                $server->update($params);
            } catch (\Exception $e) {
                abort(500, 'Lưu không thành công');
            }
            return response([
                'data' => true
            ]);
        }

        if (!ServerVmess::create($params)) {
            abort(500, 'Tạo không thành công');
        }

        return response([
            'data' => true
        ]);
    }

    public function drop(Request $request)
    {
        if ($request->input('id')) {
            $server = ServerVmess::find($request->input('id'));
            if (!$server) {
                abort(500, 'ID nút không tồn tại');
            }
        }
        return response([
            'data' => $server->delete()
        ]);
    }

    public function update(ServerVmessUpdate $request)
    {
        $params = $request->only([
            'show',
        ]);

        $server = ServerVmess::find($request->input('id'));

        if (!$server) {
            abort(500, 'Máy chủ không tồn tại');
        }
        try {
            $server->update($params);
        } catch (\Exception $e) {
            abort(500, 'Lưu không thành công');
        }

        return response([
            'data' => true
        ]);
    }

    public function copy(Request $request)
    {
        $server = ServerVmess::find($request->input('id'));
        $server->show = 0;
        if (!$server) {
            abort(500, 'Máy chủ không tồn tại');
        }
        if (!ServerVmess::create($server->toArray())) {
            abort(500, 'Sao chép không thành công');
        }

        return response([
            'data' => true
        ]);
    }
}
