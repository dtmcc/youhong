<?php
namespace app\home\controller;
use think\Db;
use think\Config;
use think\Session;
use think\Controller;
class Login extends Controller{
	//加载登录页面
	public function getLogin(){
		return $this->fetch("Login/login");
	}
	//执行登录
	public function postDologin(){
		$request=request();
		$username=$request->param("username");
		$password=$request->param("password");
		$userinfo=Db::table("member")->where("username",$username)->find();
		if (Db::table("member")->where("username",$username)->find()) {
			if ($password == $userinfo['password']) {
				Session::set('id',$userinfo['id']);
				Session::set('username',$userinfo['username']);
				$this->success("登录成功！","/homeindex/index");
			}else{
				$this->error("账户密码错误，请重新登录！","/homelogin/login");
			}
		}else{
			$this->error("用户不存在，请检查后重新登录！","/homelogin/login");
		}
	}
	//执行退出
	public function getlogout(){
        Session::delete('id');
        Session::delete('username');
      	$this->success("退出成功！","/homelogin/login");
   }

   //忘记密码
   public function getForget_password(){
   		return $this->fetch("Login/forget_password");
   }
   public function getForget_password_m(){
   		return $this->fetch("Login/forget_password_m");
   }
   public function postDoforget_password(){
   		$request=request();
		$email=$request->param("email");
		$id=Db::table("member")->where('email',$email)->value("id");
		// 接收方 主题 内容
		$re=sendmail($email,"尤洪商城,密码重置","<a href='http://www.youhong.com/homelogin/reset?id={$id}'>请点击重置密码</a>");
		if($re){
			$this->success("验证信息已发送至你的邮箱，请注意查收！");
		}
   }
   public function postDoforget_password_m(){
   		$request=request();
		$safeanswer=$request->param("safeanswer");
		$username=$request->param("username");
		$id=Db::table("member")->where('username',$username)->value("id");
		$dbsafeanswer=Db::table("member")->where('username',$username)->value("safeanswer");
		if ($dbsafeanswer == $safeanswer) {
			$this->success("密保验证通过","/homelogin/reset?id={$id}");
		}else{
			$this->error("密保验证未通过","/homelogin/forget_password_m");
		}
   }
   //获取密保问题
   public function postSafequestion(){
   		$request=request();
   		$val=$request->param("val");
   		$s_q=Db::table("member")->where("username",$val)->value("safequestion");
   		echo $s_q;
   }

   //加载重置页面
   public function getReset(){
   		$request=request();
   		$id=$request->param("id");
   		$m_info=Db::table("member")->where("id",$id)->find();
   		return $this->fetch("Login/reset",['m_info'=>$m_info]);
   }
   //执行重置
   public function postDoreset(){
   		$request=request();
   		$row=Db::table("member")->where("id",$request->param("id"))->update(['password'=>$request->param("password")]);
   		if ($row) {
   			$this->success("密码重置成功！","/homelogin/login");
   		}
   }
	
}
?>