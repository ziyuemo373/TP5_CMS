<?php
namespace app\admin\validate;

use think\Validate;

class Admin extends Validate
{
    protected $rule = [
        'email' =>  'require|email',
        'phone' => 'mobile',
        'name'  =>  'require|max:25|token',
    ];

}