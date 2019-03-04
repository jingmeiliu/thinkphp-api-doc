<?php
namespace app\docs\controller;
use think\Controller;
/**
* 
*/
class Docs extends Controller
{
	
	public function index($value='')
	{
		$projectName='接口文档';
		if (!empty($_GET['detail'])) {
		    $apiDesc = new \app\docs\model\ApiDesc($projectName);
		    $apiDesc->render();
		} else {
		    $apiList = new \app\docs\model\ApiList($projectName);
		    $apiList->render();
		}
	}
}