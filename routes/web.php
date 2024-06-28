<?php

Route::get("/", function (Illuminate\Http\Request $request) {
    if (config("v2board.app_url") && config("v2board.safe_mode_enable", 0) && $request->server("HTTP_HOST") != parse_url(config("v2board.app_url"))["host"]) {
        abort(403);
    }
    $renderParams = ["title" => config("v2board.app_name", "V2Board"), "theme" => config("v2board.frontend_theme", "default"), "version" => config("app.version"), "description" => config("v2board.app_description", "V2Board is best"), "logo" => config("v2board.logo")];
    if (!config("theme." . $renderParams["theme"])) {
        $themeService = new App\Services\ThemeService($renderParams["theme"]);
        $themeService->init();
    }
    $renderParams["theme_config"] = config("theme." . config("v2board.frontend_theme", "default"));
    return view("theme::" . config("v2board.frontend_theme", "default") . ".dashboard", $renderParams);
});
Route::get("/" . config("v2board.secure_path", config("v2board.frontend_admin_path", hash("crc32b", config("app.key")))), function (Illuminate\Http\Request $request) {
    return view("admin", ["title" => config("v2board.app_name", "V2Board"), "theme_sidebar" => config("v2board.frontend_theme_sidebar", "light"), "theme_header" => config("v2board.frontend_theme_header", "dark"), "theme_color" => config("v2board.frontend_theme_color", "default"), "background_url" => config("v2board.frontend_background_url"), "version" => config("app.version"), "logo" => config("v2board.logo"), "secure_path" => config("v2board.secure_path", config("v2board.frontend_admin_path", hash("crc32b", config("app.key"))))]);
});
Route::get("/" . (!config("v2board.staff_path") ? "webcon" : config("v2board.staff_path")), function () {
    $staffPath = config("v2board.staff_path");
    if (empty($staffPath)) {
        $staffPath = "webcon";
    }
    return view("staff", ["title" => config("v2board.app_name", "V2Board - Staff"), "theme_sidebar" => config("v2board.frontend_theme_sidebar", "light"), "theme_header" => config("v2board.frontend_theme_header", "dark"), "theme_color" => config("v2board.frontend_theme_color", "default"), "background_url" => config("v2board.frontend_background_url"), "version" => config("app.version"), "logo" => config("v2board.logo"), "staff_path" => $staffPath]);
});

Route::get("/test", function () {
    return \Tests\Test::run();
});
?>
