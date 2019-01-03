<?php
namespace app\common\model;

use think\Model;
use app\common\model\Profile as ProfileModel;
use app\common\model\Files as FileModel;
use think\facade\Log;

class TradingAccount extends Model
{
    protected $table = 'trading_account';
    protected $pk = 'id';
    protected $readonly = ['user_id','uuid'];

    public function user()
    {
        return $this->belongsTo('User','id','user_id');
    }
}