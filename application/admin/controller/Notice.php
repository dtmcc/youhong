<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
use think\Config;
class Notice extends Controller{
    //公告模块
  
  //公告列表
  public function getNotice_list(){
    $n_info=Db::table("notice")->select();
    return $this->fetch("Notice/notice_list",['n_info'=>$n_info]);
  }

  //公告添加
  public function getNotice_add(){
    return $this->fetch("Notice/notice_add");
  }

  //执行公告添加
  public function postDonotice_add(){
    $request=request();
    $file=$request->file('pic');
    if ($file) {
        //移动文件到指定目录
        $info=$file->move(ROOT_PATH . 'public' . DS . 'notice');
        //获取图像路径
        $savename=$info->getSaveName();
        $path="/notice/".str_replace('\\','/',$savename);
        $data['pic']=$path;
    }
    $data['title']=$request->param('title');
    $data['desc']=$request->param('desc');
    $row=Db::table("notice")->insert($data);
      if ($row) {
        $this->success("公告添加成功","/adminnotice/notice_list");
      }else{
        $this->error("系统繁忙，请稍后重试！","/adminnotice/notice_list");
      } 
  }

  //加载公告编辑
  public function getNotice_edit($id){
    $no_info=Db::table("notice")->where("id",$id)->find();
    return $this->fetch("Notice/notice_edit",["no_info"=>$no_info]);
  }

  //执行公告编辑
  public function postDonotice_edit(){
    $request=request();
    $file=$request->file('pic');  
    if ($file) {
        //移动文件到指定目录
        $info=$file->move(ROOT_PATH . 'public' . DS . 'notice');
        //获取图像路径
        $savename=$info->getSaveName();
        $path="/notice/".str_replace('\\','/',$savename);
        $data['pic']=$path;
        $data['title']=$request->param('title');
        $data['desc']=$request->param('desc');
        // var_dump($data);
        $old_info=Db::table("notice")->where("id",$request->param('id'))->find();
        // echo "<pre>";
        // var_dump($old_info);exit;
        $desc=$old_info["desc"];
        preg_match_all('/<img.*?src="(.*?)".*?>/is',$desc,$arr);
        if (Db::table("notice")->where("id",$request->param('id'))->update($data)) {
            //删除原图片
            @unlink(".".$old_info['pic']);
            //删除百度编辑器上传的图片
            foreach ($arr['1'] as $key => $value) {
                @unlink(".".$value);
            }
            $this->success("修改成功","/adminnotice/notice_list");
          }else{
            $this->error("系统繁忙，请稍后重试！","/adminnotice/notice_list");
          }
        
    }else{
      $data=$request->only(['title','desc']);
      $row=Db::table("notice")->where("id",$request->param('id'))->update($data);
      if ($row) {
        $this->success("修改成功","/adminnotice/notice_list");
      }else{
        $this->error("系统繁忙，请稍后重试！","/adminnotice/notice_list");
      }
    }
  }

  //公告删除
  public function getNotice_del($id){
    $info=Db::table("notice")->where("id",$id)->find();
    //获取descr
    $desc=$info["desc"];
    preg_match_all('/<img.*?src="(.*?)".*?>/is',$desc,$arr);
    if (Db::table("notice")->where("id",$id)->delete()) {
       //删除原图片
       @unlink(".".$info['pic']);
       //删除百度编辑器上传的图片
       foreach ($arr['1'] as $key => $value) {
        @unlink(".".$value);
       }
        $this->success("删除成功","/adminnotice/notice_list");
      }else{
        $this->error("系统繁忙，请稍后重试！","/adminnotice/notice_list");
      }
  }

  




}
  