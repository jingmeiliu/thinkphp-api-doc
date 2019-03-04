<?php
namespace app\admin\controller;
use app\admin\model\Admin;
use think\Controller;
use think\Db;

/**
 * 基类
 * */
class Base extends Controller
{
    protected $AdminInfo;
    protected $admin_ids;
    /*空操作*/
    public function _empty(){
        echo 404;
    }

    /**
     * 默认接口服务
     * @desc 默认接口服务，当未指定接口服务时执行此接口服务
     * @return string title 标题
     * @return string content 内容
     * @return string version 版本，格式：X.X.X
     * @return int time 当前时间戳
     * @exception 400 非法请求，参数传递错误
     */
    public function GetAdminIds(){
        $class_model= new \app\common\model\Classification();
        if($this->AdminInfo){
            if($this->AdminInfo['parent_id']==$this->AdminInfo['id']){
                //团队超管能看所有
                $admin_ids=Db::name('admin')->where('parent_id',$this->AdminInfo['id'])->column('id');
                $admin_ids=implode(',',$admin_ids);
            }else{
                //自己的职位id
                $role_id=$this->AdminInfo['role_id'];
                //自己职位下的职位(show your role's role)
                //you can do it with any method because you are the leader ,but you can't control us.
                $role_ids=$class_model->getRoleIds($role_id);
                $role_ids=implode(',',$role_ids);
                $where_admin['role_id']=['in',$role_ids];
                //自己职位下的职位下的人
                $admin_ids=Db::name('admin')->where($where_admin)->column('id');
                //加上自己
                array_push($admin_ids,$this->AdminInfo['id']);
                $admin_ids=implode(',',$admin_ids);
            }
        }else{
            return false;
        }
        return $admin_ids;
    }

   
    /**
     * 获取当前code码
     * "controller@action"
     * */
    public function getCode(){
        $request = request();
        $controller=strtolower($request->controller());
        $action=strtolower($request->action());
        $code=$controller.'@'.$action;
        return $code;
    }


}
