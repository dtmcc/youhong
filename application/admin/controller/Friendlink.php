<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
use think\Config;
class Friendlink extends Controller{
  //加载列表模板
  public function getFriendlink_list(){
    $data=Db::table("friendlink")->select();
    $arrstatus=[0=>'新申请',2=>'未通过',1=>'已通过'];
    return $this->fetch("Friendlink/friendlink_list",['arrstatus'=>$arrstatus,'data'=>$data]);
  }

  //加载添加友情链接模块
  public function getFriendlink_add(){
    return $this->fetch("Friendlink/friendlink_add");
  }

  //执行添加
  public function postDofriendlink_add(){
    $request=request();
    $data=$request->except(['action']);
    $row=Db::table("friendlink")->insert($data);
    if ($row) {
      $this->success("数据添加成功","/adminfriendlink/friendlink_list");
    }else{
      $this->error("数据添加失败，请重新添加","/adminfriendlink/friendlink_add");
    }
  }

  //加载编辑模板
  public function getFriendlink_edit(){
  	$request=request();
  	$data=Db::table("friendlink")->where("id",$request->param("id"))->find();
  	return $this->fetch("Friendlink/friendlink_edit",['data'=>$data]);
  }

  //执行编辑操作
  public function postDofriendlink_edit(){
  	$request=request();
  	$status=$request->param("status");
  	$data=$request->except(['action','id']);
  	$row=Db::table("friendlink")->where("id",$request->param('id'))->update($data);
  	if ($row) {
      if ($status==2) {
        sendmail($request->param('email'),"友情链接审核通知","您的友情链接申请未通过验证，请重新填写信息，感谢你的支持");
        $this->success("数据修改成功，未通过审核","/adminfriendlink/friendlink_list");
      }
      if ($status==1) {
        sendmail($request->param('email'),"友情链接审核通知","您的友情链接申请已通过验证，可直接在官网查询，感谢你的支持");
        $this->success("数据修改成功，已通过审核","/adminfriendlink/friendlink_list");
      }
    }else{
      $this->error("修改失败,请重新操作","/adminfriendlink/friendlink_list");
    }
  	
  }

  //执行ajax删除操作
  public function postFriendlink_del($id){
  	Db::table("friendlink")->where("id",$id)->delete();
  }

}
  