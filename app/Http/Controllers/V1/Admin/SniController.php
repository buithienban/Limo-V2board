<?php

namespace App\Http\Controllers\V1\Admin;

class SniController extends \App\Http\Controllers\Controller
{
    public function fetch(\Illuminate\Http\Request $request)
    {
        return response(["data" => \App\Models\Sni::orderBy("id", "DESC")->get()]);
    }
    public function save(\App\Http\Requests\Admin\SniSave $request)
    {
        $data = $request->only(["dname_sni", "network_settings", "content"]);
        if (!$request->input("id")) {
            if (!\App\Models\Sni::create($data)) {
                abort(500, "Lưu không thành công");
            }
        } else {
            try {
                \App\Models\Sni::find($request->input("id"))->update($data);
            } catch (\Exception $ex) {
                abort(500, "Lưu không thành công");
            }
        }
        return response(["data" => true]);
    }
    public function show(\Illuminate\Http\Request $request)
    {
        if (!$request->input("id")) {
            abort(500, "Tham số sai");
        }
        $Sni = \App\Models\Sni::find($request->input("id"));
        if (!$Sni) {
            abort(500, "Sni không tồn tại");
        }
        $Sni->show = $Sni->show ? 0 : 1;
        if (!$Sni->save()) {
            abort(500, "Lưu không thành công");
        }
        return response(["data" => true]);
    }
    public function drop(\Illuminate\Http\Request $request)
    {
        if (!$request->input("id")) {
            abort(500, "Lỗi tham số");
        }
        $Sni = \App\Models\Sni::find($request->input("id"));
        if (!$Sni) {
            abort(500, "Sni không tồn tại");
        }
        if (!$Sni->delete()) {
            abort(500, "Xóa không thành công");
        }
        return response(["data" => true]);
    }
}

?>