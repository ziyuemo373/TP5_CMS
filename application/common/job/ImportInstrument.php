<?php

namespace app\common\job;

use think\queue\Job;
use think\facade\Log;
use think\Queue;
use app\common\model\Instrument as InstrumentModel;

class ImportInstrument {

    //默认配置暂时写在开头
    private $config = [
        'file' => 'D:\Documents\Pictures\test123.csv',
        'period' => 10,
    ];

    /**
     * fire方法是消息队列默认调用的方法
     * @param Job            $job      当前的任务对象
     * @param array|mixed    $data     发布任务时自定义的数据
     */
    public function fire(Job $job,$data){
        // 如有必要,可以根据业务需求和数据库中的最新数据,判断该任务是否仍有必要执行.
        $isJobStillNeedToBeDone = $this->checkDatabaseToSeeIfJobNeedToBeDone($data);
        if(!$isJobStillNeedToBeDone){
            $job->delete();
            return;
        }
        $data += $this->config;
        $isJobDone = $this->doJob($data);

        if ($isJobDone) {
            //如果任务执行成功
            // 重发，即时执行
            $job->release($data['period']);
//            $job->release();
//            // 重发，延迟 2 秒执行
//            $job->release(2);
//            // 延迟到 2017-02-18 01:01:01 时刻执行
//            $time2wait = strtotime('2017-02-18 01:01:01') - strtotime('now');
//            $job->release($time2wait);

            print("<info>Import instrument Job has been done and will be availabe again after ".$data['period']."s</info>\n");
        }else{
            if ($job->attempts() > 3) {
                //通过这个方法可以检查这个任务已经重试了几次了
                //这种情况需要通知开发人员，并且停止这个队列了
                print("<warn>Import instrument Job has been retried more than 3 times!"."</warn>\n");
                $job->delete();
                // 也可以重新发布这个任务
                //print("<info>Import instrument Job will be availabe again after 2s."."</info>\n");
                //$job->release(2); //$delay为延迟时间，表示该任务延迟2秒后再执行
            }
        }
    }

    /**
     * 有些消息在到达消费者时,可能已经不再需要执行了
     * @param array|mixed    $data     发布任务时自定义的数据
     * @return boolean                 任务执行的结果
     */
    private function checkDatabaseToSeeIfJobNeedToBeDone($data){
        return true;
    }

    private function doJob($data) {
        try{
            if(file_exists($data['file'])){
                InstrumentModel::importInstrument($data['file']);
                return true;
            } else {
                print("<info>File ".$data['file']." do not exist.</info> \n");
                Log::record('File '.$data['file'].' do not exist','info');
                return false;
            }
        }catch (Exception $e){
            print("<info>Import instruments Error: " . $e->getMessage() . "</info> \n");
            Log::record('Import instruments Error: ' . $e->getMessage(),'info');
            return false;
        }
    }
}