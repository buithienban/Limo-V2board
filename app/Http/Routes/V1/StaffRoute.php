<?php
namespace App\Http\Routes\V1;

use Illuminate\Contracts\Routing\Registrar;

class StaffRoute
{
    public function map(Registrar $router)
    {
        $router->group([
            'prefix' => config('v2board.staff_path'),
            'middleware' => 'staff'
        ], function ($router) {
            // Config
            $router->get ('/config/fetch', 'V1\\Staff\\ConfigController@fetch');
            $router->post('/config/save', 'V1\\Staff\\ConfigController@save');
            // Ticket
            $router->get ('/ticket/fetch', 'V1\\Staff\\TicketController@fetch');
            $router->post('/ticket/reply', 'V1\\Staff\\TicketController@reply');
            $router->post('/ticket/close', 'V1\\Staff\\TicketController@close');
            // User
            $router->post('/user/update', 'V1\\Staff\\UserController@update');
            $router->get ('/user/fetch', 'V1\\Staff\\UserController@fetch');
            $router->get ('/user/getUserInfoById', 'V1\\Admin\\UserController@getUserInfoById');
            $router->post('/user/sendMail', 'V1\\Staff\\UserController@sendMail');
            $router->post('/user/ban', 'V1\\Staff\\UserController@ban');
            // Plan
            $router->get ('/plan/fetch', 'V1\\Staff\\PlanController@fetch');
            
            $router->post('/plan/sort', 'V1\\Admin\\PlanController@sort');
            // Order
            $router->get ('/order/fetch', 'V1\\Staff\\OrderController@fetch');
            $router->post('/order/detail', 'V1\\Staff\\OrderController@detail');
            $router->post('/order/assign', 'V1\\Admin\\OrderController@assign');
            // Server
            $router->get ('/server/group/fetch', 'V1\\Admin\\Server\\GroupController@fetch');
            $router->get ('/server/manage/getNodes', 'V1\\Admin\\Server\\ManageController@getNodes');
            // Stat
            $router->get ('/stat/getStat', 'V1\\Staff\\StatController@getStat');
            $router->get ('/stat/InfoStaff', 'V1\\Staff\\StatController@InfoStaff');
            $router->get ('/stat/getServerLastRank', 'V1\\Staff\\StatController@getServerLastRank');
            $router->get ('/stat/getServerTodayRank', 'V1\\Staff\\StatController@getServerTodayRank');
            $router->get ('/stat/getUserLastRank', 'V1\\Staff\\StatController@getUserLastRank');
            $router->get ('/stat/getUserTodayRank', 'V1\\Staff\\StatController@getUserTodayRank');
            $router->get ('/stat/getOrder', 'V1\\Staff\\StatController@getOrder');
            $router->get ('/stat/getStatUser', 'V1\\Staff\\StatController@getStatUser');
            $router->get ('/stat/getRanking', 'V1\\Staff\\StatController@getRanking');
            $router->get ('/stat/getStatRecord', 'V1\\Staff\\StatController@getStatRecord');
            // Notice
            $router->get ('/notice/fetch', 'V1\\Staff\\NoticeController@fetch');
            $router->post('/notice/save', 'V1\\Staff\\NoticeController@save');
            $router->post('/notice/show', 'V1\\Staff\\NoticeController@show');
            $router->post('/notice/update', 'V1\\Staff\\NoticeController@update');
            $router->post('/notice/drop', 'V1\\Staff\\NoticeController@drop');

            // Knowledge
            $router->get ('/knowledge/fetch', 'V1\\Staff\\KnowledgeController@fetch');
            $router->get ('/knowledge/getCategory', 'V1\\Staff\\KnowledgeController@getCategory');
            $router->post('/knowledge/save', 'V1\\Staff\\KnowledgeController@save');
            $router->post('/knowledge/show', 'V1\\Staff\\KnowledgeController@show');
            $router->post('/knowledge/drop', 'V1\\Staff\\KnowledgeController@drop');
            $router->post('/knowledge/sort', 'V1\\Staff\\KnowledgeController@sort');
        });
    }
}
