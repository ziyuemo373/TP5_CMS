<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//Route::rule('index/hello/:name','index/Index/hello');
//Route::rule('admin/index','admin/Admin/index');
Route::rule('backend/index','admin/Index/index');
Route::rule('backend/welcome','admin/Index/welcome');
Route::rule('backend/login','admin/Login/index');
Route::rule('backend/role','admin/Role/index');

//devel test url
Route::rule('backend/test','admin/Test/index');

//Admin
Route::rule('backend/admin','admin/Admin/index');
Route::rule('backend/admin/add','admin/Admin/addAdmin');
Route::rule('backend/admin/update/:id','admin/Admin/updateAdmin');
Route::rule('backend/admin/updatestatus','admin/Admin/updateStatus');
Route::rule('backend/admin/del','admin/Admin/delAdmin');
Route::rule('backend/admin/delmul','admin/Admin/delMulAdmin');

//User
Route::rule('backend/user','admin/User/index');
Route::rule('backend/user/add','admin/User/addUser');
Route::rule('backend/user/update/:id','admin/User/updateUser');
Route::rule('backend/user/updatestatus','admin/User/updateStatus');
Route::rule('backend/user/del','admin/User/delUser');
Route::rule('backend/user/delmul','admin/User/delMulUser');
Route::rule('backend/user/uploadicon','admin/User/uploadIcon');
Route::rule('backend/user/delicon','admin/User/delIcon');

//Trading
Route::rule('backend/trading','admin/Trading/instrumentIndex');
Route::rule('backend/trading/add','admin/Trading/addInstrument');
Route::rule('backend/trading/update/:id','admin/Trading/updateInstrument');
Route::rule('backend/trading/updatestatus','admin/Trading/updateInstrumentStatus');
Route::rule('backend/trading/del','admin/Trading/delInstrument');
Route::rule('backend/trading/delmul','admin/Trading/delMulInstrument');
Route::rule('backend/trading/importins','admin/Trading/importInstrument');

//API
Route::rule('api/user/register','api/User/register','POST')
    ->middleware('app\api\middleware\RquestFrequencyCheck');
Route::rule('api/user/login','api/User/login','POST')
    ->middleware('app\api\middleware\RquestFrequencyCheck');
Route::rule('api/user/edit','api/User/editProfile','POST')
    ->middleware(['app\api\middleware\RquestFrequencyCheck','app\api\middleware\Auth']);
Route::rule('api/user/view','api/User/viewProfile','GET')
    ->middleware(['app\api\middleware\RquestFrequencyCheck','app\api\middleware\Auth']);
Route::rule('api/user/:uuid$','api/User/viewUserProfile','GET')
    ->middleware(['app\api\middleware\RquestFrequencyCheck','app\api\middleware\Auth','app\api\middleware\UserCheck']);
Route::rule('api/instrument/:instrument_id$','api/Instrument/instrument','GET')
    ->middleware('app\api\middleware\RquestFrequencyCheck');
//Route::get('think', function () {
//    return 'hello,ThinkPHP5!';
//});
//
//Route::get('hello/:name', 'index/hello');

return [

];
