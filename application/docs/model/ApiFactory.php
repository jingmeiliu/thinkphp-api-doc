<?php
namespace app\docs\model;

use app\docs\model\BadRequestException;
use app\docs\model\InternalServerErrorException;

/**
 * ApiFactory 创建控制器类 工厂方法
 *
 * 将创建与使用分离，简化客户调用，负责控制器复杂的创建过程
 *
```
 *      //根据请求(?service=XXX.XXX)生成对应的接口服务，并进行初始化
 *      $api = ApiFactory::generateService();
```
 * @package     PhalApi\Api
 * @license     http://www.phalapi.net/license GPL 协议 GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2014-10-02
 */

class ApiFactory {

    /**
     * 创建服务器
     * 根据客户端提供的接口服务名称和需要调用的方法进行创建工作，如果创建失败，则抛出相应的自定义异常
     *
     * 创建过程主要如下：
     * - 1、 是否缺少控制器名称和需要调用的方法
     * - 2、 控制器文件是否存在，并且控制器是否存在
     * - 3、 方法是否可调用
     * - 4、 控制器是否初始化成功
     *
     * @param boolen $isInitialize 是否在创建后进行初始化
     * @param string $_REQUEST['service'] 接口服务名称，格式：XXX.XXX
     * @return \PhalApi\Api 自定义的控制器
     *
     * @uses \PhalApi\Api::init()
     * @throws BadRequestException 非法请求下返回400
     */
    static function generateService($isInitialize = TRUE) {
        $service    = input('service');
        $arr=explode("/",$service);
        $namespace = $arr[0];
        $api        = $arr[1];
        $action     = $arr[2];
        if (empty($api) || empty($action)) {
            throw new BadRequestException(
                T('service ({service}) illegal', array('service' => $service))
            );
        }

        $apiClass = 'app'.'\\' . str_replace('_', '\\', $namespace)
            . '\\Controller\\' . str_replace('_', '\\', ucfirst($api));

        if (!class_exists($apiClass)) {
            throw new Exception('no such service as:[' . $service . ']');
        }
        if (!method_exists($apiClass, $action) || !is_callable(array($apiClass, $action))) {
            throw new Exception('no1 such service as:[' . $service . ']');
        }
        if ($isInitialize) {
            $api->init();
        }
        return $apiClass;
    }
	
}
