<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
//导入Config.php
use think\Config;
use think\Session;
class Login extends Controller
{
   // 加载登陆
    public function getLogin(){
       return $this->fetch("Login/login");
    }

    //执行登录
    public function postDologin(){
      $request=request();
      $username=$request->param("username");
      $status=Db::table("member")->where("username",$username)->value("status");
      $id=Db::table("member")->where("username",$username)->value("id");
      $password=$request->param("password");
      $row=Db::table("member")->where("username='{$username}' and password='{$password}'")->select();
      if ($row) {
        if ($status == 2) {
          //登录成功
          Session::set('id',$id);
          Session::set('username',$username);
          $list=Db::query("select n.name,n.mname,n.aname from member_role as mr,role_node as rn,node as n where mr.rid=rn.rid and rn.nid=n.id and mid=$id");
          //初始化，让所有用户有主页权限
          $nodelist['index'][]='getindex';
          foreach($list as $key=>$value){
             // 给数组$nodelist赋值
             $nodelist[$value['mname']][]=$value['aname'];
             // // 判断如果有add方法添加insert方法 如果有edit方法添加update方法
             if($value['aname']=="get".$value['mname']."_add"){
              $nodelist[$value['mname']][]="postdo".$value['mname']."_add";
             }
             if($value['aname']=="get".$value['mname']."_edit"){
              $nodelist[$value['mname']][]="postdo".$value['mname']."_edit";
             }
          }
          Session::set('nodelist',$nodelist);
          $this->success("登陆成功","/adminindex/index");
        }else{
          $this->error("抱歉，亲不是管理员用户，无法登录后台","/adminlogin/login");
        }
      }else{
        $this->error("账户或密码错误，登陆失败","/adminlogin/login");
      }
    }
  //执行退出的方法
  public function getLogout(){
    Session::delete("id");
    Session::delete('username');
    Session::delete('nodelist');
    $this->success("退出成功","/adminlogin/login");
  }

  //切换用户
  public function getLogout_q(){
    Session::delete("id");
    Session::delete('username');
    Session::delete('nodelist');
    $this->success("该账户已退出成功，请登录新的账户","/adminlogin/login");
  }
   public function post1Dologin(){
   		// 创建请求对象
   		$request=request();
   		$uname=$request->param("username");
   		$pas=$request->param("password");
   		$row=Db::table("user")->where("username='{$uname}' and password='{$pas}'")->select();
   		if($row){
   			// 存储session信息
   			Session::set('islogins',$row[0]['id']);
        Session::set('username',$uname);
        //获取当前用户登陆所具有的全部权限信息 
        $list=Db::query("select n.name,n.mname,n.aname from user_role as ur,role_node as rn,node as n where ur.rid=rn.rid and rn.nid=n.id and uid={$row[0]['id']}");
          // echo "<pre>";
          // var_dump($list);exit;
        //初始化权限 让所有得管理员都有访问后台的权限
        $nodelist['index'][]='getindex';
        
        // 便利 作用就是便于后面的判断
         foreach($list as $key=>$value){
            // 给数组$nodelist赋值
            $nodelist[$value['mname']][]=$value['aname'];
           
             // 判断如果有add方法添加insert方法 如果有edit方法添加update方法
             if($value['aname']=="getadd"){
              $nodelist[$value['mname']][]='postinsert';
             }

             if($value['aname']=="getedit"){
              $nodelist[$value['mname']][]='postupdate';
             }
         }
         // echo "<pre>";
         // var_dump($nodelist);exit;
         // 把用户具有的权限信息存储在session里面 在allow里面做判断
        Session::set('nodelist',$nodelist);
   			$this->success("登陆成功","/index/index");
   		}else{
   			$this->error("登陆失败","/login/login");
   		}
   }
}
