<?php
namespace app\docs\model;
use think\Model;


/**
 * ApiOnline - 在线接口文档
 *     
 * @package     PhalApi\Helper
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2017-11-22
 */

class ApiOnline extends Model{

    protected $projectName;

    public function __construct($projectName) {
        $this->projectName = $projectName;
    }

    /**
     * @param string $tplPath 模板绝对路径
     */
    public function render($tplPath = NULL) {
        header('Content-Type:text/html;charset=utf-8');
    }

    public function getApiRules($pai) {
        $rules = array();

        $allRules = $pai->getRules();
        if (!is_array($allRules)) {
            $allRules = array();
        }
        $allRules = array_change_key_case($allRules, CASE_LOWER);
        $arr=explode("/",input('service'));
        $action     = $arr[2];
        $action = strtolower($action);
        if (isset($allRules[$action]) && is_array($allRules[$action])) {
            $rules = $allRules[$action];
        }

        if (isset($allRules['*'])) {
            $rules = array_merge($allRules['*'], $rules);
        }

        //$apiCommonRules = DI()->config->get('app.apiCommonRules', array());
        $apiCommonRules = [];
        if (!empty($apiCommonRules) && is_array($apiCommonRules)) {
            // fixed issue #22
            if ($this->isServiceWhitelist()) {
                foreach ($apiCommonRules as &$ruleRef) {
                    $ruleRef['require'] = false;
                }
            }

            $rules = array_merge($apiCommonRules, $rules);
        }
        return $rules;
    }
}
