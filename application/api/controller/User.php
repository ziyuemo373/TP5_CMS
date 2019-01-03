<?php
namespace app\api\controller;

use app\common\model\User as UserModel;
use think\facade\Request;
use think\facade\Log;
use think\facade\Session;

//code: 2000
class User extends ApiBase
{

    public function register(){
        $user = UserModel::where('name', Request::post('name'))->find();
        if (!empty($user)) {
            return json(array('status'=>0,'code'=>'2001','msg'=>'用户名已使用'));
        }
        $new_user = new UserModel;
        $data = Request::only(['name','password','phone','email']);
        $data['status'] = 1;
        $res = $new_user->addUser($data);
        if (!empty($res)) {
            return json(array('status'=>1,'code'=>'2002','msg'=>'注册成功'));
        } else {
            return json(array('status'=>0,'code'=>'2003','msg'=>'注册失败'));
        }
    }

    public function login(){
        if(Session::get('account_user')){
            //logout the pre user first
        }
        $name = Request::post('name');
        $password = Request::post('password');
//            Log::record('test login param '.print_r(array($is_rem,$pwd,$captcha,$rempsw),1),'info');

        $account_user = UserModel::where('name', $name)->find();
        if (empty($account_user)) {
            return json(array('status'=>0,'msg'=>'当前用户不存在或者用户名错误'));
        }
        if(!password_verify($password, $account_user->password)){
            return json(array('status'=>0,'msg'=>'密码有误'));
        }
//            if($account_user->status == 0){
//                return json(array('status'=>0,'msg'=>'当前用户禁止登录'));
//            }

        Session::set('account_user',$name);
        $account_user->updateUser(array('last'=>time()));
        Log::record('login ip：'.request()->ip().'——name：'.$name.' --time: '.date('Y-m-d H:i:s', time()),'info');
        return json([
            'status'=>1,
            'msg'=>'success',
            'uuid'=>$account_user->profile->uuid]);
    }

    public function editProfile(){
        $nick_name = Request::post('nick_name');
        $sex = Request::post('sex');
        $icon = Request::post('icon');
        $account_user = request()->loginUser;

        $upload_path = env('root_path').'uploads/'.'profile/' . date('Ymd') . '/';
        $file_name = md5(microtime(true)).'.png';
        $icon_path = 'profile/' . date('Ymd') . '/'.$file_name;
        $file_path = $upload_path.$file_name;
        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0777, true);
        }
        if (!empty($icon) && file_put_contents($file_path, base64_decode($icon))){
            if(!empty($account_user->profile->icon)){
                $account_icon = $account_user->profile->icon;
                $path = env('root_path').'uploads'.DIRECTORY_SEPARATOR.$account_icon->path;
                if(is_file($path)){
                    unlink($path);
                }
                $account_icon->path = $icon_path;
                $account_icon->save();
            } else {
                $account_user->profile->icon()->save(['path' => $icon_path, 'created' => time()]);
            }
        }
        $res = $account_user->updateUser(['nickName'=>$nick_name,'sex'=>$sex]);
        if($res){
            return $this->successResult();
        } else {
            return $this->failResult();
        }
    }

    public function viewProfile(){
        $account_user = request()->loginUser;
        return json([
            'status'=>0,
            'result'=>[
                'uuid' => $account_user->profile->uuid,
                'nick_name' => $account_user->profile->nick_name,
                'sex' => $account_user->profile->sex,
                'icon' => $_SERVER['REQUEST_SCHEME'] .'://' . $_SERVER['HTTP_HOST'] . str_replace('/public/index.php' ,'' ,$_SERVER['SCRIPT_NAME']) . '/uploads/'.$account_user->profile->icon->path,
            ],
        ]);
    }

    public function viewUserProfile(){
        $request_user = request()->requestUser;
        Log::record('test11'.print_r($request_user,1),'info');
        return json([
            'status'=>0,
            'result'=>[
                'uuid' => $request_user->profile->uuid,
                'nick_name' => $request_user->profile->nick_name,
                'icon' => empty($request_user->profile->icon) ? '' : $_SERVER['REQUEST_SCHEME'] .'://' . $_SERVER['HTTP_HOST'] . str_replace('/public/index.php' ,'' ,$_SERVER['SCRIPT_NAME']) . '/uploads/'.$request_user->profile->icon->path,
            ],
        ]);
    }
}