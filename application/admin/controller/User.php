<?php

namespace app\admin\controller;

use app\common\model\User as UserModel;
use app\common\model\Files as FileModel;
use app\common\model\Profile as ProfileModel;
use think\facade\Request;
use think\facade\Log;

class User extends Base
{
    public function index()
    {
        $keyword = !empty(Request::post('keyword')) ? Request::post('keyword') : '';
        if(!empty($keyword)){
            $data = UserModel::where('name', 'like', "%$keyword%")->limit(3)->order('id', 'asc')->select();
        } else {
            $data = UserModel::limit(20)->order('id', 'asc')->select();
        }
        $num = count($data);
        $this->assign('data',$data);
        $this->assign('keyword',$keyword);
        $this->assign('num',$num);
        return $this->fetch('user-index');
    }

    public function addUser(){
        if (request()->post()) {
            $user = UserModel::where('name', Request::post('name'))->find();
            if (!empty($user)) {
                return json_encode(array('status'=>0,'msg'=>'当前名称已存在'));
            }
            $new_user = new UserModel;
            // 过滤post数组中的非数据表字段数据
            $data = Request::only(['name','password','status','nickName','sex','img_file_id']);
            $res = $new_user->addUser($data);
            if (!empty($res)) {
                return json_encode(array('status'=>1,'msg'=>'添加成功'));
            } else {
                return json_encode(array('status'=>0,'msg'=>'添加失败'));
            }
        } else {
            return $this->fetch('user-add');
        }
    }

    public function delUser(){
        $id = input('id');
        $user = UserModel::get($id);
        if (empty($user)) {
            return json_encode(array('status'=>0,'msg'=>'信息有误'));
        }
        $res = $user->deleteUser();
        if (!$res) {
            return json_encode(array('status'=>0,'msg'=>'删除失败'));
        } else {
            return json_encode(array('status'=>1,'msg'=>'删除成功'));
        }
    }

    public function delMulUser(){
        $ids = input('ids');
        $users = UserModel::all($ids);
        if (empty($users)) {
            return json_encode(array('status'=>0,'msg'=>'信息有误'));
        }
        $result = '';
        $status = 0;
        $deleted = [];
        foreach ($users as $user){
            $res = $user->deleteUser();
            if (!$res && empty($result)) {
                $result = '删除失败';
            } else {
                $deleted[] = $user->id;
            }
        }
        if(empty($result)){
            $status = 1;
            $result = '删除成功';
        }
        return json_encode(array('status'=>$status,'msg'=>$result,'deleted'=>$deleted));
    }

    public function updateUser(){
        $id = input('id');
        if (request()->post()) {
            $user = UserModel::get($id);
            $data = Request::only(['name','password','status','nickName','sex']);
            $res = $user->updateUser($data);
            if ($res) {
                return json_encode(array('status'=>1,'msg'=>'编辑成功','url'=>'admin/user/index'));
            } else {
                return json_encode(array('status'=>0,'msg'=>'编辑失败'));
            }
        } else {
            $user = UserModel::get($id);
            if (empty($user)) {
                $this->error('信息有误');
            }
            $this->assign('info',$user);
            return $this->fetch('user-add');
        }
    }

    public function updateStatus(){
        $id = input('id');
        $status = input('status');
        $user = UserModel::get($id);
        if(empty($user)){
            return json_encode(array('status'=>0,'msg'=>'信息有误'));
        }
        $res = $user->updateUser(array('status'=>$status));
        if (!$res) {
            return json_encode(array('status'=>0,'msg'=>'修改失败'));
        } else {
            return json_encode(array('status'=>1,'msg'=>'修改成功'));
        }
    }

    public function uploadIcon(){
        if(Request::post('user_id') == 0){
            $file = request()->file('image_file');
            $info = $file->move('../uploads/profile');
            if ($info) {
                $new_file = new FileModel;
                $new_file = $new_file->create(['relate_id'=>0,
                    'relate_type'=>'app\common\model\Profile',
                    'path' => 'profile/' . $info->getSaveName(),
                    'created' => time()]);
                if ($new_file) {
                    return response(json_encode(array(
                        'status' => 1,
                        'msg' => '上传成功',
                        'id' => $new_file->id,
                        'path' => $new_file->path,
                    )));
                } else {
                    return response(json_encode(array('status' => 0, 'msg' => '上传失败')));
                }
            }
        } else {
            $user = UserModel::get(Request::post('user_id'));
            if (!empty($user)) {
                $file = request()->file('image_file');
                $info = $file->move('../uploads/profile');
                if ($info) {
                    $res = $user->profile->icon()->save(['path' => 'profile/' . $info->getSaveName(), 'created' => time()]);
                    if ($res) {
                        return response(json_encode(array(
                            'status' => 1,
                            'msg' => '上传成功',
                            'id' => $user->profile->icon->id,
                            'path' => $user->profile->icon->path,
                        )));
                    } else {
                        return response(json_encode(array('status' => 0, 'msg' => '上传失败')));
                    }
                } else {// 上传失败获取错误信息
//                echo $file->getError();
                    return response(json_encode(array('status' => 0, 'msg' => '上传失败')));
                }
            } else {
                return response(json_encode(array('status' => 0, 'msg' => '上传失败')));
            }
        }
    }

    public function delIcon(){
        $file = FileModel::get(Request::post('file_id'));
        if(!empty($file)) {
            $res = $file->delete();
            if($res){
                //use reponse() to forced output html.
                return response(json_encode(array('status'=>1,'msg'=>'删除成功')));
            } else {
                return response(json_encode(array('status'=>0,'msg'=>'删除失败')));
            }
        } else {
            return response(json_encode(array('status'=>0,'msg'=>'删除失败')));
        }
    }

}