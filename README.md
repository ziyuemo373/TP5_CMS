# TP5_CMS
个人用Think PHP5写的一个例子项目，主要用于练习和熟悉TP5。

1. controller, model, view的例子。
1.1. 简单的后台登录。
1.2. 简单的接口实现。
2. think-queue队列的例子。
3. think-captcha验证码的例子。
4. phpmailer发送邮件的例子
5. h-ui的使用。http://www.h-ui.net/Hui-3.7-Hui-iconfont.shtml
6. redis的使用。
6.1. 接口中的RquestFrequencyCheck中间件需要用到redis，如果还没配置好，可以在route.php中先去掉这个检查。

一、安装（windows下）
参考https://www.kancloud.cn/manual/thinkphp5_1/353948

打开cmd命令行
cd到工作目录，例如E:\Workspace\phpStudy\PHPTutorial\WWW

composer create-project topthink/think cms

之后就会创建E:\Workspace\phpStudy\PHPTutorial\WWW\cms 这个目录到，TP5也会安装到里面

引入需要使用的扩展包

composer require topthink/think-captcha 1.*

composer require phpmailer/phpmailer

composer install thinkphp-queue


下载完框架后，把Git的文件覆盖进去就可以了。

二、创建数据库
运行sql/cms.sql

之后启动http server就可以了。
php和DB的配置省略。

如果需要rewrite url，可以参考https://www.kancloud.cn/manual/thinkphp5_1/353955