<?php
namespace app\common\model;

use think\Model;

class Profile extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'profile';
    protected $pk = 'id';
    protected $readonly = ['user_id','uuid'];

    public function icon(){
        return $this->morphOne('Files','relate');
    }
}