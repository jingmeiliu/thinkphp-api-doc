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
//    public function _initialize()
//    {
//        parent::_initialize();
//        header("Access-Control-Allow-Origin: *");
//        header("Access-Control-Allow-Headers: X-token,Origin, X-Requested-With, Content-Type, Accept");
//        header('Access-Control-Allow-Methods: GET, POST, PUT');
//        if(strtoupper($_SERVER['REQUEST_METHOD'])== 'OPTIONS'){
//            exit;
//        }
//        /*管理员信息*/
//        $this->AdminInfo = Admin::GetAdminInfo();
//        $this->admin_ids= $this->GetAdminIds();
//        if($this->AdminInfo && $this->AdminInfo['is_disable']==0){
//            /*获取当前操作码*/
//            $code=$this->getCode();
//            /*获取不需要权限的权限码集合*/
//            $model=new \app\admin\model\Auth();
//            $not_check=$model->getNotCheck();
//            if(in_array($code,$not_check)){
//                return true;
//            }else{
//                /*获取当前用户的权限*/
//                $rights=Admin::getRight($this->AdminInfo);
//                if($rights){
//                    $rights=explode(',',$rights);
//                    /*判断是否有权限*/
//                    if(in_array($code,$rights)){
//                        return true;
//                    }else{
//                        echo 101;exit;//无权限
//                    }
//                }else{
//                    echo 101;exit;//无当前操作权限
//                }
//            }
//        }else{
//            echo 100;exit;//登录信息有误
//        }
//    }

    /**
     * 获取我职位下的人（包括我）
     * */
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

    /*
     *
     *
     *
     * */

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

    /**
     *
     * */

}
