<?php
namespace app\admin\controller;

use app\common\model\Instrument as InstrumentModel;
use think\facade\Request;
use think\facade\Log;


class Trading extends Base
{
    public function instrumentIndex()
    {
        $nameKeyword = !empty(Request::post('name_keyword')) ? Request::post('name_keyword') : '';
        $idKeyword = !empty(Request::post('id_keyword')) ? Request::post('id_keyword') : '';
        $query = InstrumentModel::limit(20)->order('id', 'asc');
        if(!empty($nameKeyword)){
            $query->where("name like :nameKeyword1 OR chinese_name like :nameKeyword2");
            $query->bind(['nameKeyword1'=>"%$nameKeyword%",'nameKeyword2'=>"%$nameKeyword%"]);
        }
        if(!empty($idKeyword)){
            $query->where('instrument_id', 'like', "%$idKeyword%");
        }
        $data = $query->select();
        $num = count($data);
        $this->assign('data',$data);
        $this->assign('name_keyword',$nameKeyword);
        $this->assign('id_keyword',$idKeyword);
        $this->assign('num',$num);
        return $this->fetch('instrument-index');
    }

    public function addInstrument(){
        if (request()->post()) {
            $instrument = InstrumentModel::where('instrument_id', Request::post('instrument_id'))->find();
            if (!empty($instrument)) {
                return json_encode(array('status'=>0,'msg'=>'当前股票已存在'));
            }
            $new_instrument = new InstrumentModel;
            // 过滤post数组中的非数据表字段数据
            $data = Request::only(['instrument_id','market','name','chinese_name','status']);
            $res = $new_instrument->addInstrument($data);
            if (!empty($res)) {
                return json_encode(array('status'=>1,'msg'=>'添加成功'));
            } else {
                return json_encode(array('status'=>0,'msg'=>'添加失败'));
            }
        } else {
            return $this->fetch('instrument-add');
        }
    }

    public function delInstrument(){
        $id = input('id');
        $instrument = InstrumentModel::get($id);
        if (empty($instrument)) {
            return json_encode(array('status'=>0,'msg'=>'信息有误'));
        }
        $res = $instrument->deleteInstrument();
        if (!$res) {
            return json_encode(array('status'=>0,'msg'=>'删除失败'));
        } else {
            return json_encode(array('status'=>1,'msg'=>'删除成功'));
        }
    }

    public function delMulInstrument(){
        $ids = input('ids');
        $instruments = InstrumentModel::all($ids);
        if (empty($instruments)) {
            return json_encode(array('status'=>0,'msg'=>'信息有误'));
        }
        $result = '';
        $status = 0;
        $deleted = [];
        foreach ($instruments as $instrument){
            $res = $instrument->deleteInstrument();
            if (!$res && empty($result)) {
                $result = '删除失败';
            } else {
                $deleted[] = $instrument->id;
            }
        }
        if(empty($result)){
            $status = 1;
            $result = '删除成功';
        }
        return json_encode(array('status'=>$status,'msg'=>$result,'deleted'=>$deleted));
    }

    public function updateInstrument(){
        $id = input('id');
        if (request()->post()) {
            $instrument = InstrumentModel::get($id);
            $data = Request::only(['name','password','status','nickName','sex']);
            $res = $instrument->updateInstrument($data);
            if ($res) {
                return json_encode(array('status'=>1,'msg'=>'编辑成功','url'=>'admin/Trading/instrumentIndex'));
            } else {
                return json_encode(array('status'=>0,'msg'=>'编辑失败'));
            }
        } else {
            $instrument = InstrumentModel::get($id);
            if (empty($instrument)) {
                $this->error('信息有误');
            }
            $this->assign('info',$instrument);
            return $this->fetch('instrument-add');
        }
    }

    public function updateInstrumentStatus(){
        $id = input('id');
        $status = input('status');
        $instrument = InstrumentModel::get($id);
        if(empty($instrument)){
            return json_encode(array('status'=>0,'msg'=>'信息有误'));
        }
        $res = $instrument->updateInstrument(array('status'=>$status));
        if (!$res) {
            return json_encode(array('status'=>0,'msg'=>'修改失败'));
        } else {
            return json_encode(array('status'=>1,'msg'=>'修改成功'));
        }
    }

    public function importInstrument(){
        $file = request()->file('add_file');
        $info = $file->move('../uploads/files');
        if ($info) {
            InstrumentModel::importInstrument($info->getRealPath());
            return response(json_encode(array(
                'status' => 1,
                'msg' => '成功',
            )));
        }
        return response(json_encode(array('status' => 0, 'msg' => '失败')));
    }

}