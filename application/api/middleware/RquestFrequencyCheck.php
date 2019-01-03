<?php
namespace app\api\middleware;

use think\facade\Session;
use think\facade\Cache;

class RquestFrequencyCheck
{
    public function handle($request, \Closure $next)
    {
        if(!empty(Session::get('account_user'))){
            $cacheKey = Session::get('account_user').$request->url();
            $value = Cache::get($cacheKey);
            if(!empty(Cache::get($cacheKey))){
                return json(array('status'=>0,'code'=>1002,'msg'=>'请求过于频繁'),501);
            } else {
                Cache::set($cacheKey,true,3);
                return $next($request);
            }
        } else {
            return $next($request);
        }

    }
}