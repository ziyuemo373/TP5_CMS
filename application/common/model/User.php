<?php
namespace app\common\model;

use think\Model;
use app\common\model\Profile as ProfileModel;
use app\common\model\Files as FileModel;
use app\common\job\EmailSend;
use think\facade\Log;

class User extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'user';
    protected $pk = 'id';
    protected $readonly = ['name'];

    public function profile(){
        return $this->hasOne('Profile','user_id');
    }
    public function tradingAccounts()
    {
        return $this->hasMany('TradingAccount','user_id');
    }

    // 设置当前模型的数据库连接
//    protected $connection = 'db_config';
    public function addUser($data){
        $data['password'] = pswCrypt($data['password']);
        if(!isset($data['created'])){
            $data['created'] = time();
        }
        $profile = new ProfileModel;
        $profile->nick_name = !empty($data['nickName']) ? $data['nickName'] : '';
        $profile->sex = !empty($data['sex']) ? $data['sex'] : 'secret';
        $profile->uuid = uuid();
        unset($data['nickName']);
        unset($data['sex']);
        $this->profile = $profile;

        $res = $this->together('profile,tradingAccounts')->save($data);
        if(!empty($res)) {
            $this->tradingAccounts()->save([
                'type' => 'mock',
                'uuid' => uuid(),
                'status' => 1,
            ]);
        }
        if(isset($data['img_file_id'])){
            $icon = FileModel::get($data['img_file_id']);
            if(!empty($icon)){
                $icon->relate_id = $this->profile->id;
                $icon->save();
            }
        }
        if($res){
            $this->sendWelcomeEmail();
        }
        return $res;
    }

    private function sendWelcomeEmail(){
        if(!empty($this->getAttr('email'))) {
            $jobData = [
                'toAddress' => $this->getAttr('email'),
                'toName' => $this->getAttr('name'),
                'subject' => '欢迎注册',
                'body' => '欢迎使用ThinkPHP5！',
            ];
            EmailSend::createEmailJob($jobData);
        }
    }

    public function updateUser($data){
        if(isset($data['password'])) {
            if (pswCrypt($data['password']) != $this->getAttr('password')) {
                $data['password'] = pswCrypt($data['password']);
            } else {
                unset($data['password']);
            }
        }
        if(!empty($data['nickName'])){
            $profile_data['nick_name'] = $data['nickName'];
        }
        if(!empty($data['sex'])){
            $profile_data['sex'] = $data['sex'];
        }
        if(!empty($profile_data)){
            if(empty($this->profile)){
                $this->profile()->save($profile_data);
            } else {
                $this->profile->save($profile_data);
            }
        }
        //不允许改name，详细查看$readonly
        return $this->save($data);
    }

    public function deleteUser(){
        $icon = $this->profile->icon;
        $icon->delete();
        //需要删除profile
        return $this->together('profile')->delete();
    }
}