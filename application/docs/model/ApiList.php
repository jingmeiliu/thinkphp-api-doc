<?php
namespace app\docs\model;
use think\Model;
use app\docs\model\ApiOnline;


/**
 * ApiList - 在线接口列表文档 - 辅助类
 *
 * @package     PhalApi\Helper
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2017-11-22
 */

class ApiList extends ApiOnline {
    public function GetFile()
    {
        $files=[
            'admin\controller\Basic',
        ];
        return $files;
    }
    public function GetFilterMethod()
    {
        $files=['_empty','__construct','getRules','_initialize'];
        return $files;
    }


    public function render($tplPath = NULL) {
        parent::render($tplPath);
        $allApiS = array();
        $files_arr=$this->GetFile();
        $files=[];
        $allPhalApiApiMethods=$this->GetFilterMethod();
        foreach ($files_arr as $k => $v) {
            $files[]='app\\'.$v;
        }
        foreach ($files as $aFile) {
            $apiClassName = $aFile;

             if (!class_exists($apiClassName)) {
                 continue;
             }
            //  左菜单的标题

            $ref        = new \ReflectionClass($apiClassName);
            $title      = "//请检测接口服务注释($apiClassName)";
            $desc       = '//请使用@desc 注释';
            $isClassIgnore = false; // 是否屏蔽此接口类
            $docComment = $ref->getDocComment();
//                print_r($docComment);exit;
            if ($docComment !== false) {
                $docCommentArr = explode("\n", $docComment);
                $comment       = trim($docCommentArr[1]);
                $title         = trim(substr($comment, strpos($comment, '*') + 1));
                foreach ($docCommentArr as $comment) {
                    $pos = stripos($comment, '@desc');
                    if ($pos !== false) {
                        $desc = substr($comment, $pos + 5);
                    }

                    if (stripos($comment, '@ignore') !== false) {
                        $isClassIgnore = true;
                    }
                }
            }

            if ($isClassIgnore) {
                continue;
            }

            $namespace=explode('\\',$aFile)[1];//模块
            $apiClassShortName=explode('\\',$aFile)[3];//控制器
//                print_r($namespace);exit;
            $allApiS[$namespace][$apiClassShortName]['title'] = $title;
            $allApiS[$namespace][$apiClassShortName]['desc']  = $desc;
            $allApiS[$namespace][$apiClassShortName]['methods'] = array();
            print_r($allPhalApiApiMethods);
            $method = array_diff(get_class_methods($apiClassName), $allPhalApiApiMethods);
            sort($method);
            foreach ($method as $mValue) {
                $rMethod = new \Reflectionmethod($apiClassName, $mValue);
                if (!$rMethod->isPublic() || strpos($mValue, '__') === 0) {
                    continue;
                }

                $title      = '//请检测函数注释';
                $desc       = '//请使用@desc 注释';
                $isMethodIgnore = false;
                $docComment = $rMethod->getDocComment();
                if ($docComment !== false) {
                    $docCommentArr = explode("\n", $docComment);
                    $comment       = trim($docCommentArr[1]);
                    $title         = trim(substr($comment, strpos($comment, '*') + 1));

                    foreach ($docCommentArr as $comment) {
                        $pos = stripos($comment, '@desc');
                        if ($pos !== false) {
                            $desc = substr($comment, $pos + 5);
                        }

                        if (stripos($comment, '@ignore') !== false) {
                            $isMethodIgnore = true;
                        }
                    }
                }

                if ($isMethodIgnore) {
                    continue;
                }

                $service = trim($namespace, '\\') . '/' . $apiClassShortName . '/' . ucfirst($mValue);
                $allApiS[$namespace][$apiClassShortName]['methods'][$service] = array(
                    'service' => $service,
                    'title'   => $title,
                    'desc'    => $desc,
                );
            }
        }
//        echo '<pre>';print_r($allApiS);exit;
        // 运行模式
        $env = (PHP_SAPI == 'cli') ? TRUE : FALSE;
        $webRoot = '';
        if ($env) {
            $trace = debug_backtrace();
            $listFilePath = $trace[0]['file'];
            $webRoot = substr($listFilePath, 0, strrpos($listFilePath, DS));
        }

        // 主题风格，fold = 折叠，expand = 展开
        $theme = isset($_GET['type']) ? $_GET['type'] : 'fold';
        global $argv;
        if ($env) {
            $theme = isset($argv[1]) ? $argv[1] : 'fold';
        }
        if (!in_array($theme, array('fold', 'expand'))) {
            $theme = 'fold';
        }

        //echo json_encode($allApiS) ;
        // 字典排列与过滤
        foreach ($allApiS as $namespace => &$subAllApiS) {
            ksort($subAllApiS);
            if (empty($subAllApiS)) {
                unset($allApiS[$namespace]);
            }
        }
        unset($subAllApiS);

        $projectName = $this->projectName;

        $tplPath = !empty($tplPath) ? $tplPath : dirname(__FILE__) . '/api_list_tpl.php';
        include $tplPath;
    }
}

function listDir($dir) {
    $dir .= substr($dir, -1) == DS ? '' : DS;
    $dirInfo = array();
    foreach (glob($dir . '*') as $v) {
        if (is_dir($v)) {
            $dirInfo = array_merge($dirInfo, listDir($v));
        } else {
            $dirInfo[] = $v;
        }
    }
    return $dirInfo;
}

function saveHtml($webRoot, $name, $string){
    $dir = $webRoot . DS . 'docs';
    if (!is_dir ( $dir)){
        mkdir ( $dir);
    }
    $handle = fopen ( $dir . DIRECTORY_SEPARATOR . $name . '.html', 'wb');
    fwrite ( $handle, $string);
    fclose ( $handle);
}

