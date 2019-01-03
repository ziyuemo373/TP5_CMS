<?php
namespace app\api\controller;

use app\common\model\Instrument as InstrumentModel;
use think\facade\Request;
use think\facade\Log;
use think\facade\Session;

//code: 2000
class Instrument extends ApiBase
{
    public function instrument($instrument_id){
        $instrument = InstrumentModel::where('instrument_id','like','%'.$instrument_id.'%')->find();
        if(!empty($instrument)){
            return json([
                'status'=>0,
                'result'=>[
                    'instrument_id' => $instrument->instrument_id,
                    'market' => $instrument->market,
                    'name' => $instrument->name,
                    'chinese_name' => $instrument->chinese_name,
                ],
            ]);
        } else {
            return json(array('status'=>0,'msg'=>'股票不存在'),501);
        }

    }
}