<?php

namespace app\common\model;

use think\Model;
use app\common\model\Profile as ProfileModel;
use app\common\model\Files as FileModel;
use think\facade\Log;

class Instrument extends Model
{
    protected $table = 'instrument';
    protected $pk = 'id';
    protected $readonly = ['instrument_id'];

    public function addInstrument($data){
        $res = $this->save($data);
        return $res;
    }

    public function updateInstrument($data){
        //不允许改instrument_id，详细查看$readonly
        return $this->save($data);
    }

    public function deleteInstrument(){
        return $this->delete();
    }

    public static function importInstrument($path){
        $data_arr = array();
        $header = array();
        if (($handle = fopen($path, "r")) !== FALSE) {
            while ($data_line = fgetcsv($handle, 10000)) {
                if (empty($header)) {
                    $header = $data_line;
                    continue;
                }
                $each_data = array();
                foreach ($header as $csv_key_num => $csv_key_name) {
                    $each_data[$csv_key_name] = $data_line[$csv_key_num];
                }
                $data_arr[] = $each_data;
            }
            fclose($handle);
        }
        if(!empty($data_arr)){

            //第1种方法，先查询，后更新或者新增
            foreach ($data_arr as $data){
//                $instrument = Instrument::getByInstrumentId($data['instrument_id']);
                $instrument = Instrument::where('instrument_id',$data['instrument_id'])->find();
                if(empty($instrument)){
                    Instrument::create([
                        'instrument_id'  => $data['instrument_id'],
                        'market' => $data['market'],
                        'name' => $data['name'],
                        'chinese_name' => $data['chinese_name'],
                        'status' => 1,
                    ]);
                } else {
                    $instrument->setAttr('market',$data['market']);
                    $instrument->setAttr('name',$data['name']);
                    $instrument->setAttr('chinese_name',$data['chinese_name']);
                    $instrument->setAttr('status',1);
                    $instrument->save();
                }
            }

            //第2种方法，全部删除后，批量更新

            //第3种方法，如果需要支持status部分更新的话，还是需要先查询后更新
            //只是要细节一点
        }
    }
}