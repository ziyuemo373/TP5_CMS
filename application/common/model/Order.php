<?php
namespace app\common\model;

use think\Model;
use app\common\model\Profile as ProfileModel;
use app\common\model\Files as FileModel;
use think\facade\Log;

/**
 * 暂时不实现订单功能，这个项目的主要目标是熟悉TP5框架。
 * 而订单功能涉及到非常具体的业务逻辑，本人不太熟悉，
 * 会花费大量时间去了解，所以不实现了。
 */
class Order extends Model
{
    protected $table = 'order';
    protected $pk = 'id';
    protected $readonly = ['order_id'];

}