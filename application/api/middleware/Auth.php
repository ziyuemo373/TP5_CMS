<?php
namespace app\api\middleware;

use think\facade\Session;
use app\common\model\User as UserModel;

class Auth
{
    public function handle($request, \Closure $next)
    {
        if(empty(Session::get('account_user'))){
            return json(array('status'=>0,'code'=>1001,'msg'=>'无效的用户会话'),501);
        } else {
            $name = Session::get('account_user');
            $account_user = UserModel::where('name', $name)->find();
            if(!empty($account_user)){
                $request->loginUser = $account_user;
                return $next($request);
            } else {
                return json(array('status'=>0,'code'=>1001,'msg'=>'无效的用户会话'),501);
            }
        }
    }
}