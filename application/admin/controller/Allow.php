<?php
namespace app\admin\controller;
use think\Session;
use think\Controller;
class Allow extends Controller
{
   //初始化initialize
    public function _initialize(){
        if(!Session::get("id")){
           $this->error("请先登录","/adminlogin/login");  
        }
        // 获取session信息
        $nodelist=Session::get('nodelist');
        //获取到控制器和方法
        $request=request();
        $controller=strtolower($request->controller());
        $action=$request->action();
        echo $controller."_".$action;
        // 对比判断 （登陆获取的控制器和方法 and session里面存在的） ||或者
        // 先判断控制器是否存在 在判断方法是否在在这个控制器里面
    	if(empty($nodelist[$controller]) ||!in_array($action,$nodelist[$controller])){
    		$this->error("权限不足，无法访问！ 请联系超级管理员","/adminindex/index");
    		exit;
    	}
    }
}