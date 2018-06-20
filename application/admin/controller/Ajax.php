<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
use think\Config;
class Ajax extends Controller{
  //此页面专门存放ajax控制器的方法
  
  /*------------------member控制器中的ajax----------------------------*/
  
  //会员禁用时的ajax状态修改 禁用
  public function postMember_status_stop($id){
    $row=Db::table("member")->where("id",$id)->update(['status'=>0]);
  }
  //会员启用时的ajax状态修改  启用
  public function postMember_status_start($id){
    $row=Db::table("member")->where("id",$id)->update(['status'=>1]);
  }
  //会员添加时的ajax用户名验证
  public function getDoajax_username($username){
    $row=Db::table("member")->where('username',$username)->find();
    if ($row) {echo "00";}
  }
  //会员添加时的ajax邮箱验证
  public function getDoajax_email($email){
    $row=Db::table("member")->where('email',$email)->find();
    if ($row) {echo "00";}
  }

  /*-----------------------admin控制器中的ajax---------------------*/

  //ajax取消管理员权限
  public function postAdmin_status_stop($id){
    $row=Db::table("member")->where("id",$id)->update(['status'=>1]);
  }




/*----------------------friendlink控制器中的ajax-------------------*/
/*----------------------login控制器中的ajax-------------------*/
/*----------------------index控制器中的ajax-------------------*/
/*----------------------order控制器中的ajax-------------------*/

//ajax执行订单发货
public function postOrder_status($id){
  $row=Db::table("order")->where('id',$id)->update(['status'=>2]);
}

/*----------------------role控制器中的ajax-------------------*/
/*----------------------notice控制器中的ajax-------------------*/

//公告管理的ajax删除
public function postNotice_del($id){
  $pic=Db::table("notice")->where("id",$id)->value("pic");
  @unlink(".".$pic);
  $row=Db::table("notice")->where('id',$id)->delete();
}


/*----------------------message控制器中的ajax-------------------*/


  
}

?>