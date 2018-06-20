<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
use think\Config;
class Adver extends Controller{
      //广告版位
  
  //版位列表
  public function getAdver_list(){
    $request=request();
    $a_info=Db::table("adver")->select();
    return $this->fetch("Adver/adver_list",['a_info'=>$a_info]); 
  }

  //版位编辑
  public function getAdver_edit($id){
    $ad_info=Db::table("adver")->where("id",$id)->find();
    return $this->fetch("Adver/adver_edit",["ad_info"=>$ad_info]);
  }

  //版位执行编辑
  public function postDoadver_edit(){
    $request=request();
    $data=$request->only(['adv_name','adv_des']);
    $row=Db::table("adver")->where("id",$request->param('id'))->update($data);
    if ($row) {
      $this->success("数据修改成功！","/adminadver/adver_list");
    }else{
      $this->error("系统繁忙，请稍后重试！","/adminadver/adver_list");
    }
  }

  //版位删除
  public function postAdver_del($id){
    $row=Db::table("adver")->where("id",$id)->delete();
  }

}
  