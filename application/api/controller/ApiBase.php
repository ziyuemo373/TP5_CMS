<?php
namespace app\api\controller;

use think\Controller;
use think\facade\Log;
use think\facade\Session;
use app\admin\model\Admin;

//code: 1000
class ApiBase extends Controller
{
    protected function initialize()
    {
//        $admin_name = Session::get('account_user');
//        if (empty($admin_name)) {
//
//        } else {
//
//        }
    }

    protected function successResult(){
        return json(array('status'=>1,'msg'=>'成功'));
    }
    protected function failResult(){
        return json(array('status'=>0,'msg'=>'失败'));
    }
}