<?php
namespace app\admin\controller;

use think\Controller;
use think\facade\Session;
use think\facade\Cookie;
use think\facade\Log;
use app\admin\model\Admin;

class Login extends Controller
{
    public function index()
    {
        if(Session::get('admin_user')){
            $this->redirect(url('admin/Index/index','',''));
        }else{
            if (request()->post()) {
                //login submit
                $name = input('post.name');
                $pwd = input('post.pwd');
                $pwd = pswCrypt($pwd);
                $rempsw = input('post.rempsw');

                if(empty($name)||empty($pwd)||empty($pwd)){
                    return json_encode(array('status'=>0,'msg'=>'用户名或密码,验证码不可为空'));
                }
//                if(!captcha_check($captcha)){
//                    return json_encode(array('status'=>0,'msg'=>'验证码错误'));
//                }

                $admin_user = Admin::where('name', $name)->find();
                if (empty($admin_user)) {
                    return json_encode(array('status'=>0,'msg'=>'当前用户不存在或者用户名错误'));
                }
                if(password_verify($pwd, $admin_user->password)){
                    return json_encode(array('status'=>0,'msg'=>'密码有误'));
                }
                if($admin_user->status == 0){
                    return json_encode(array('status'=>0,'msg'=>'当前用户禁止登录'));
                }

                Session::set('admin_user',$name);
                if($rempsw == 1){
                    //记住密码 存储于cookie
                    //感觉不是很安全，有待改善
                    cookie('cu',trim($name),3600*24*30);
                } else {
                    Cookie::delete('cu');
                }

                $admin_user->last = time();
                $admin_user->save();
                Log::record('login ip：'.request()->ip().'——name：'.$name.' --time: '.date('Y-m-d H:i:s', time()),'info');
                return json_encode(array('status'=>1,'msg'=>'登录成功'));
            } else {
                //login page
                $name = Cookie::get('cu');
                if($name) {
                    $this->assign('name',$name);
                }
                return $this->fetch('login');
            }
        }
    }

}