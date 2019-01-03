<?php
namespace app\common\job;

use think\queue\Job;
use think\facade\Log;
use PHPMailer\PHPMailer\PHPMailer;
use think\Queue;

class EmailSend {

    //配置暂时写在开头
    private $host = 'smtp.163.com';
    // 设置邮件内容的编码
    private $charSet='UTF-8';
        // SMTP username
    private $username = '';
        // SMTP password
    private $password = '';
        // 启用TLS加密，`ssl`也被接受
    private $secure = 'ssl';
        // 连接的TCP端口
    private $port = 994;
        //设置发件人
    private $fromAddress = '';
    private $fromName = '';

    /**
     * @param $jobData = [
     *   'toAddress' => 'test@qq.com',
     *   'toName' => 'Tester',
     *   'subject' => 'test subject',
     *   'body' => 'test body haha',
     *  ]
     */
    public static function createEmailJob($jobData){
        // 1.当前任务将由哪个类来负责处理。
        //   当轮到该任务时，系统将生成一个该类的实例，并调用其 fire 方法
        $jobHandlerClassName = 'app\common\job\EmailSend';
        // 2.当前任务归属的队列名称，如果为新队列，会自动创建
        $jobQueueName = "emailSendJobQueue";
        // 3.当前任务所需的业务数据 . 不能为 resource 类型，其他类型最终将转化为json形式的字符串
        //   ( jobData 为对象时，需要在先在此处手动序列化，否则只存储其public属性的键值对)

        // 4.将该任务推送到消息队列，等待对应的消费者去执行
        $isPushed = Queue::push( $jobHandlerClassName , $jobData , $jobQueueName );
        // database 驱动时，返回值为 1|false  ;   redis 驱动时，返回值为 随机字符串|false
        if( $isPushed !== false ){
            return true;
        }else{
            return false;
        }
    }

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

        $isJobDone = $this->doEmailSendJob($data);

        if ($isJobDone) {
            //如果任务执行成功， 记得删除任务
            $job->delete();
            print("<info>EmailSend Job has been done and deleted"."</info>\n");
        }else{
            if ($job->attempts() > 3) {
                //通过这个方法可以检查这个任务已经重试了几次了
                print("<warn>EmailSend Job has been retried more than 3 times!"."</warn>\n");
                $job->delete();
                // 也可以重新发布这个任务
                //print("<info>EmailSend Job will be availabe again after 2s."."</info>\n");
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

    /**
     * 根据消息中的数据进行实际的业务处理
     * @param array|mixed    $data     发布任务时自定义的数据
     * $data = [
     *   'toAddress' => 'testabc@test.com',
     *   'toName' => 'test name',
     *   'subject' => 'test subject',
     *   'body' => 'test body',
     * ]
     * @return boolean                 任务执行的结果
     */
    private function doEmailSendJob($data) {
        // 根据消息中的数据进行实际的业务处理...
//        print("<info>EmailSend Job Started. job Data is: ".var_export($data,true)."</info> \n");
//        print("<info>EmailSend Job is Fired at " . date('Y-m-d H:i:s') ."</info> \n");
//        print("<info>EmailSend Job is Done!"."</info> \n");

        $mail = new PHPMailer();
        try{
            //邮件调试模式
            $mail->SMTPDebug = 1;
            //设置邮件使用SMTP
            $mail->isSMTP();
            // 设置邮件程序以使用SMTP
            $mail->Host = $this->host;
            // 设置邮件内容的编码
            $mail->CharSet=$this->charSet;
            // 启用SMTP验证
            $mail->SMTPAuth = true;
            // SMTP username
            $mail->Username = $this->username;
            // SMTP password
            $mail->Password = $this->password;
            // 启用TLS加密，`ssl`也被接受
            $mail->SMTPSecure = $this->secure;
            // 连接的TCP端口
            $mail->Port = $this->port;
            //设置发件人
            $mail->setFrom($this->fromAddress, $this->fromName);
            //  添加收件人1
            $mail->addAddress($data['toAddress'], empty($data['toName']) ? '' : $data['toName']);     // Add a recipient
//            $mail->addAddress('ellen@example.com');               // Name is optional
//            收件人回复的邮箱
            $mail->addReplyTo($this->fromAddress, $this->fromName);
//            抄送
//            $mail->addCC('cc@example.com');
//            $mail->addBCC('bcc@example.com');
            //附件
//            $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//            $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
            //Content
            // 将电子邮件格式设置为HTML
            $mail->isHTML(true);
            $mail->Subject = $data['subject'];
            $mail->Body    = $data['body'];
//            $mail->AltBody = '这是非HTML邮件客户端的纯文本';
            $mail->send();
            return true;
        }catch (Exception $e){
            print("<info>Mailer Error: " . $mail->ErrorInfo . "</info> \n");
            Log::record('Mailer Error: ' . $mail->ErrorInfo,'info');
            return false;
        }
    }
}