<?php
namespace app\admin\model;

use think\Model;

class Admin extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'admin';
    protected $pk = 'id';

    // 设置当前模型的数据库连接
//    protected $connection = 'db_config';
}