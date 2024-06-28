<?php

namespace App\Http\Controllers\V1\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\NoticeSave;
use App\Models\Notice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NoticeController extends Controller
{
    public function fetch(Request $request)
    {
        $currentUserId = $request->user['id'];
        $notices = Notice::whereRaw("FIND_IN_SET(?, id_staff)", [$currentUserId])
                        ->orderBy('id', 'DESC')
                        ->get();

        return response([
            'data' => $notices
        ]);
    }

    public function save(NoticeSave $request)
    {
        $currentUserId = $request->user['id'];
        $data = $request->only([
            'title',
            'content',
            'img_url',
            'tags'
        ]);

        if (!$request->input('id')) {
            $data['id_staff'] = $currentUserId;
            if (!Notice::create($data)) {
                abort(500, '保存失败');
            }
        } else {
            $notice = Notice::find($request->input('id'));
            // Check if 'id_staff' contains more than one ID
            if (strpos($notice->id_staff, ',') !== false) {
                abort(500, 'Bạn không có quyền sửa thông báo này');
            }
            try {
                $notice->update($data);
            } catch (\Exception $e) {
                abort(500, '保存失败');
            }
        }
        return response([
            'data' => true
        ]);
    }



    public function show(Request $request)
    {
        if (empty($request->input('id'))) {
            abort(500, '参数有误');
        }
        $notice = Notice::find($request->input('id'));
        if (!$notice) {
            abort(500, '公告不存在');
        }
        $notice->show = $notice->show ? 0 : 1;
        if (!$notice->save()) {
            abort(500, '保存失败');
        }

        return response([
            'data' => true
        ]);
    }

    public function drop(Request $request)
    {
        if (empty($request->input('id'))) {
            abort(500, '参数错误');
        }
        $notice = Notice::find($request->input('id'));
        if (!$notice) {
            abort(500, '公告不存在');
        }
        if (!$notice->delete()) {
            abort(500, '删除失败');
        }
        return response([
            'data' => true
        ]);
    }
}
