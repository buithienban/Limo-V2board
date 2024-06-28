<?php

namespace App\Http\Controllers\V1\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\KnowledgeSave;
use App\Http\Requests\Staff\KnowledgeSort;
use App\Models\Knowledge;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KnowledgeController extends Controller
{
    public function fetch(Request $request)
    {
        $currentUserId = $request->user['id'];

        if ($request->input('id')) {
            $knowledge = Knowledge::where('id_staff', $currentUserId)
                ->find($request->input('id'))
                ->toArray();
            if (!$knowledge) abort(500, '知识不存在');
            return response([
                'data' => $knowledge
            ]);
        }
        return response([
            'data' => Knowledge::where('id_staff', $currentUserId)
                ->select(['title', 'id', 'updated_at', 'category', 'show'])
                ->orderBy('sort', 'ASC')
                ->get()
        ]);
    }

    public function getCategory(Request $request)
    {
        return response([
            'data' => array_keys(Knowledge::get()->groupBy('category')->toArray())
        ]);
    }

    public function save(KnowledgeSave $request)
    {
        $params = $request->validated();
        $currentUserId = $request->user['id'];

        if (!$request->input('id')) {
            $params['id_staff'] = $currentUserId;
            if (!Knowledge::create($params)) {
                abort(500, '创建失败');
            }
        } else {
            $knowledge = Knowledge::find($request->input('id'));
            if (!$knowledge) {
                abort(500, '知识不存在');
            }
            if (strpos($knowledge->id_staff, ',') !== false) {
                abort(500, 'Bạn không có quyền sửa hướng dẫn này');
            }
            try {
                $knowledge->update($params);
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
        $knowledge = Knowledge::find($request->input('id'));
        if (!$knowledge) {
            abort(500, '知识不存在');
        }
        $knowledge->show = $knowledge->show ? 0 : 1;
        if (!$knowledge->save()) {
            abort(500, '保存失败');
        }

        return response([
            'data' => true
        ]);
    }

    public function sort(KnowledgeSort $request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->input('knowledge_ids') as $k => $v) {
                $knowledge = Knowledge::find($v);
                $knowledge->timestamps = false;
                $knowledge->update(['sort' => $k + 1]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            abort(500, '保存失败');
        }
        DB::commit();
        return response([
            'data' => true
        ]);
    }

    public function drop(Request $request)
    {
        if (empty($request->input('id'))) {
            abort(500, '参数有误');
        }
        $knowledge = Knowledge::find($request->input('id'));
        if (!$knowledge) {
            abort(500, '知识不存在');
        }
        if (!$knowledge->delete()) {
            abort(500, '删除失败');
        }

        return response([
            'data' => true
        ]);
    }
}
