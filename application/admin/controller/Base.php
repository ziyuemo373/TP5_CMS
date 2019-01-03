<?php

namespace app\admin\controller;

use think\Controller;
use think\facade\Log;
use think\facade\Session;
use app\admin\model\Admin;

class Base extends Controller
{
    /**
     * 需要支持token，界面的post请求都需要验证token。
     * 就在Base里面自动处理。
     */
    protected function initialize()
    {
        $admin_name = Session::get('admin_user');
//        Log::record('test base '.print_r($admin_name,1),'info');
        if (empty($admin_name)) {
//            Log::record('1111 ','info');
            $this->redirect(url('admin/Login/index', '', ''));
        } else {
            $admin_user = Admin::where('name', $admin_name)->find();
            $role = [
                'role_name' => 'test role',
            ];
            //暂时写死，之后改成用权限管理
            $menuInfo = [
                [
                    'name' => '后台管理',
                    'cmenu' => [
                        ['url' => url('admin/Admin/index','',''), 'name' => '后台用户列表'],
                        ['url' => url('admin/Role/index','',''), 'name' => '角色管理'],
                        //暂时没有实现计划任务界面，功能是使用TP5 queue来处理的。详细可以查看common/job/ImportInstrument.php
//                        ['url' => url('admin/Schedule/index','',''), 'name' => '计划任务管理'],
                    ],
                ],
                [
                    'name' => '交易管理',
                    'cmenu' => [
                        ['url' => url('admin/Trading/instrumentIndex','',''), 'name' => '股票列表'],
                    ],
                ],
                [
                    'name' => '前台管理',
                    'cmenu' => [
                        ['url' => url('admin/User/index','',''), 'name' => '前台用户列表'],
                    ],
                ],
                [
                    'name' => '开发管理',
                    'cmenu' => [
                        ['url' => url('admin/Test/index','',''), 'name' => '开发者页面']
                    ],
                ],
            ];
            $this->assign('menuInfo', $menuInfo);
            $this->assign('admin', $admin_user->name);
            $this->assign('role', $role);
        }
    }
}