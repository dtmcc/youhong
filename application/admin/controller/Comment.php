<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
use think\Config;
class Comment extends Controller{
    //评论模块

  //加载评论列表
  public function getComment_list(){
    $com_info=Db::table("comment")->select();
    foreach ($com_info as $key => $value) {
        $com_info[$key]['p_info']=Db::table("product")->where("id",$value['p_id'])->find();
        $com_info[$key]['o_code']=Db::table("order")->where("id",$value['o_id'])->value("order_code");
        $com_info[$key]['m_username']=Db::table("member")->where("id",$value['m_id'])->value("username");
    }
    // echo "<pre>";
    // var_dump($com_info);exit;
    return $this->fetch("Comment/comment_list",['com_info'=>$com_info]);
  }

}
  