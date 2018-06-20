<?php
namespace app\home\controller;
use think\Db;
use think\Config;
use think\Session;
use think\Controller;
class Register extends Controller{
	//定义一个生成唯一邀请码的函数
		function createCode($m_id) {
	    static $source_string = 'E5FCDG3HQA4B1NOPIJ2RSTUV67MWX89KLYZ';
	    $num = $m_id;
	    $code = '';
	    while ( $num > 0) {
	        $mod = $num % 35;
	        $num = ($num - $mod) / 35;
	        $code = $source_string[$mod].$code;
	    }
	    if(empty($code[3]))
	        $code = str_pad($code,4,'0',STR_PAD_LEFT);
	    return $code;
		}
	//定义一个解析该唯一邀请码的函数
		function decode($matt) {
	    static $source_string = 'E5FCDG3HQA4B1NOPIJ2RSTUV67MWX89KLYZ';
	    if (strrpos($matt, '0') !== false)
	        $matt = substr($matt, strrpos($matt, '0')+1);
	    $len = strlen($matt);
	    $matt = strrev($matt);
	    $num = 0;
	    for ($i=0; $i < $len; $i++) {
	        $num += strpos($source_string, $matt[$i]) * pow(35, $i);
	    }
	    return $num;
		}

	public function getRegister(){
		$request=request();
		$p_matt=$request->param("p_matt");
	 	return $this->fetch("Register/register",['p_matt'=>$p_matt]);
	}

	//验证
	public function postcheckuser(){
		$username=$_POST['username'];
		$row=Db::table("member")->where('username',$username)->find();
		if($row){echo 1;}
	}
	public function postcheckemail(){
		$email=$_POST['email'];
		$row=Db::table("member")->where('email',$email)->find();
		if($row){echo 1;}
	}
	// 手机号码验证码
	public function postphones(){
		$s=$_POST['phone'];
		phone($s);
	}

	// 执行注册
	public function postDoregister(){
		// 创建请求
		$request=request();
		$code=$request->param("code");//手机验证码
		$p_matt=$request->param("p_matt");
		$data=$request->except(['repassword','code','action','p_matt']);
		$data['jointime']=time();
		if ($p_matt === "") {
			$data['pid'] = 0;
			$data['gid'] = 0;
			$data['pdid'] = 0;
		}else{
			$data['pid']=$this->decode($p_matt);
			$data['gid']=Db::table("member")->where("id",$data['pid'])->value("pid");
			$data['pgid']=Db::table("member")->where("id",$data['gid'])->value("pid");
		}
		// var_dump($data);exit;
		if(Session::get('code')==$code){
			$id=Db::name('member')->insertGetId($data);
			$matt=$this->createCode($id);
			$row=Db::table("member")->where("id",$id)->update(['matt'=>$matt]);
			$this->success("恭喜你，注册成功","/homelogin/login");
		}else{
			$this->error("验证码错误，请重新获取","/homeregister/register");
		}	
	}	
}
?>