<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
use think\Config;
class Message extends Controller{
    //留言列表
    public function getMessage_list(){
      $request=request();
      $status=$request->param('status');
      if ($status == "0") {
        $mes_info=Db::table("message")->where("status",0)->select();
      }else{
        $mes_info=Db::table("message")->select();
      }
      foreach ($mes_info as $key => $value) {
        $mes_info[$key]['username']=Db::table("member")->where("id",$value['m_id'])->value("username");
      }
      $statusarr=['0'=>'新留言','1'=>'已回复'];
      return $this->fetch("Message/message_list",["mes_info"=>$mes_info,'statusarr'=>$statusarr]);
    }

    //留言编辑（回复）
    public function getMessage_edit(){
      $request=request();
      $id=$request->param("id");
      $mes_info=Db::table("message")->where("id",$id)->find();
      $mes_info['username']=Db::table("member")->where("id",$mes_info['m_id'])->value("username");
      // echo "<pre>";
      // var_dump($mes_info);exit;
      return $this->fetch("Message/message_edit",['mes_info'=>$mes_info]);
    }

    //留言执行回复
    public function postDomessage_edit(){
      $request=request();
      $data['id']=$request->param("id");
      $data['replay']=$request->param("replay");
      $data['status']=1;
      // echo "<pre>";
      // var_dump($data);exit;
      $row=Db::table("message")->where("id",$data['id'])->update($data);
      if ($row) {
        $this->success("留言回复成功！","/adminmessage/message_list");
      }else{
        $this->error("系统繁忙，请稍后重试！","/adminmessage/message_list");
      }
    }

    //留言ajax删除
}
  