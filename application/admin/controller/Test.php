<?php
namespace app\admin\controller;

use app\common\model\User as UserModel;
use app\common\model\Files as FileModel;
use app\common\model\Profile as ProfileModel;
use app\common\model\Instrument as InstrumentModel;
use think\facade\Request;
use think\facade\Log;
use think\Console;
use think\Db;
use PHPMailer\PHPMailer\PHPMailer;
use think\Exception;
use think\Queue;
use think\facade\Cache;


class Test extends Base
{
    public function index()
    {
        //这是一个测试页面，专门用于单步测试代码的，只用于开发。
        Log::record('test complete '.print_r(request(),1),'info');
    }



}