<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * [pswCrypt description]密码加密
 * @param  [type] $psw [description]
 * @return [type]      [description]
 */
function pswCrypt($psw){
//    $psw = md5($psw);
//    $salt = substr($psw,0,4);
//    $psw = crypt($psw,$salt);
    return password_hash($psw,PASSWORD_BCRYPT);
}

/**
 * [uuid description]生成uuid
 * @param  [type] $prefix [description] UUID的前缀
 * @return [type] string  [description] UUID字符串
 */
function uuid($prefix = '')
{
    $chars = md5(uniqid(mt_rand(), true));
    $uuid  = substr($chars,0,8);
    $uuid .= substr($chars,8,4);
    $uuid .= substr($chars,12,4);
    $uuid .= substr($chars,16,4);
    $uuid .= substr($chars,20,12);
    return $prefix . $uuid;
}
