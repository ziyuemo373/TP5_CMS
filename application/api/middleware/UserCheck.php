<?php
namespace app\api\middleware;

use think\facade\Session;
use app\common\model\User as UserModel;

class UserCheck
{
    public function handle($request, \Closure $next)
    {
        if(!empty($request->param('uuid'))){
            $users = UserModel::hasWhere('profile', ['uuid'=>$request->param('uuid')])->select();
            if(!empty($users) && count($users) == 1){
                $request->requestUser = $users[0];
                return $next($request);
            }
        }
        return json(array('status'=>0,'code'=>1001,'msg'=>'请求用户无效'),501);
    }
}