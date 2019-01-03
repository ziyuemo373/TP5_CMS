<?php
namespace app\common\model;

use think\Model;

class Files extends Model
{
    protected $table = 'files';
    protected $pk = 'id';

    public function relate()
    {
        return $this->morphTo('relate',[
            'Profile'	=>	'app\common\model\Profile',
        ]);
    }

    public function setPathAttr($value)
    {
        //保存到数据库的路径统一使用'/'，不管是哪种系统
        return str_replace('\\','/',$value);
    }

    public function delete(){
        $path = env('root_path').'uploads'.DIRECTORY_SEPARATOR.$this->getAttr('path');
        if(is_file($path)){
            unlink($path);
        }
        return parent::delete();
    }
}