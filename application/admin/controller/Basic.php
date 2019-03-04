<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Admin;
use app\admin\model\BasicSetting;
use think\Db;


/**
 * 测试控制器
 * */

class Basic extends Controller
{
    /*空操作*/
    public function _empty(){
        echo 404;
    }
    public function getRules() {
        return array(
            'index' => array(
                'username' 	=> array('name' => 'username', 'default' => 'jingmei', 'desc' => '用户名'),
            ),
        );
    }
    /**
     * 首页测试接口
     * @desc 测试看看？
     * @return string title 标题
     * @return int time 当前时间戳
     * @exception 400 非法请求，参数传递错误
     */
    public function index(){
        $res= array(
            'title' => 'Hello ' . input('username'),
            'time' => time(),
        );
        return json($res);
    }















}

