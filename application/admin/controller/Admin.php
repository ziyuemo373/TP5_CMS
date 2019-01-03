<?php

namespace app\admin\controller;

use app\admin\model\Admin as AdminModel;
use app\admin\model\Role as RoleModel;
use think\facade\Request;

use think\facade\Log;

class Admin extends Base
{
    public function index()
    {
        $keyword = !empty(Request::post('keyword')) ? Request::post('keyword') : '';
        if(!empty($keyword)){
            $data = AdminModel::where('name', 'like', "%$keyword%")->limit(3)->order('id', 'asc')->select();
        } else {
            $data = AdminModel::limit(20)->order('id', 'asc')->select();
        }
        $num = count($data);
        $this->assign('data',$data);
        $this->assign('keyword',$keyword);
        $this->assign('num',$num);
        return $this->fetch('admin-index');
    }

    public function addAdmin(){
        if (request()->post()) {
            $admin_user = AdminModel::where('name', Request::post('name'))->find();
            if (!empty($admin_user)) {
                return json_encode(array('status'=>0,'msg'=>'当前名称已存在'));
            }
            $new_admin_user = new AdminModel;
            // 过滤post数组中的非数据表字段数据
            $data = Request::only(['name','email','phone','password','status','__token__']);
            $data['password'] = pswCrypt($data['password']);
            $data['created'] = time();
            $validateResult = $this->validate($data, 'app\admin\validate\Admin');
            Log::record('test complete '.print_r($validateResult,1),'info');
            if($validateResult !== true){

                return json_encode(array('status'=>0,'msg'=>$validateResult));
            }
            $res = $new_admin_user->save($data);
            if ($res) {
                return json_encode(array('status'=>1,'msg'=>'添加成功'));
            } else {
                return json_encode(array('status'=>0,'msg'=>'添加失败'));
            }
        } else {
            $data = RoleModel::select();
            $this->assign('data',$data);
            return $this->fetch('admin-add');
        }
    }

    public function delAdmin(){
        $id = input('id');
        $admin_user = AdminModel::get($id);
        if (empty($admin_user)) {
            return json_encode(array('status'=>0,'msg'=>'信息有误'));
        }
        if ($admin_user->id == 1) {
            return json_encode(array('status'=>0,'msg'=>'admin用户不可删除'));
        }
        $res = $admin_user->delete();
        if (!$res) {
            return json_encode(array('status'=>0,'msg'=>'删除失败'));
        } else {
            return json_encode(array('status'=>1,'msg'=>'删除成功'));
        }
    }

    public function delMulAdmin(){
        $ids = input('ids');
        $admin_users = AdminModel::all($ids);
        if (empty($admin_users)) {
            return json_encode(array('status'=>0,'msg'=>'信息有误'));
        }
        $result = '';
        $status = 0;
        $deleted = [];
        foreach ($admin_users as $admin_user){
            if ($admin_user->id == 1) {
                $result = 'admin用户不可删除';
            }else{
                $res = $admin_user->delete();
                if (!$res && empty($result)) {
                    $result = '删除失败';
                } else {
                    $deleted[] = $admin_user->id;
                }
            }
        }
        if(empty($result)){
            $status = 1;
            $result = '删除成功';
        }
        return json_encode(array('status'=>$status,'msg'=>$result,'deleted'=>$deleted));
    }

    public function updateAdmin(){
        $id = input('id');
        if (request()->post()) {
            $admin_user = AdminModel::get($id);
            $data = Request::only(['name','email','phone','password','status']);
            if(pswCrypt($data['password']) != $admin_user->password){
                $data['password'] = pswCrypt($data['password']);
            } else {
                unset($data['password']);
            }

            if ($admin_user->id == 1 && $data['status']==2) {
                return json_encode(array('status'=>0,'msg'=>'admin用户不可修改状态为禁用'));
            }

            //不允许改name
            //可以用只读字段实现，但是当前还是使用这样的定法。
            unset($data['name']);
            foreach ($data as $data_key => $data_value){
                $admin_user->{$data_key} = $data_value;
            }
            $res = $admin_user->save();
            if ($res) {
                return json_encode(array('status'=>1,'msg'=>'编辑成功','url'=>'admin/admin/index'));
            } else {
                return json_encode(array('status'=>0,'msg'=>'编辑失败'));
            }
        } else {
            $admin_user = AdminModel::get($id);
            if (empty($admin_user)) {
                $this->error('信息有误');
            }
            $this->assign('info',$admin_user);
            $data = RoleModel::select();
            $this->assign('data',$data);
            return $this->fetch('admin-add');
        }
    }

    public function updateStatus(){
        $id = input('id');
        $status = input('status');
        $admin_user = AdminModel::get($id);
        if(empty($admin_user)){
            return json_encode(array('status'=>0,'msg'=>'信息有误'));
        }
        if ($admin_user->id == 1 && $status==2) {
            return json_encode(array('status'=>0,'msg'=>'admin用户不可修改状态为禁用'));
        }
        $admin_user->status = $status;
        $res = $admin_user->save();
        if (!$res) {
            return json_encode(array('status'=>0,'msg'=>'修改失败'));
        } else {
            return json_encode(array('status'=>1,'msg'=>'修改成功'));
        }
    }
}