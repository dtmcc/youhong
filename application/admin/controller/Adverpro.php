<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
use think\Config;
class Adverpro extends Controller{
      //模板数据

  //版位数据添加
  public  function getAdverpro_add($id){
  	return $this->fetch("Adverpro/adverpro_add",['id'=>$id]); 
  }

  //版位数据执行添加
  public function postDoadverpro_add(){
    $request=request();
    $file=$request->file('picurl');
    //移动文件到指定目录
    $info=$file->move(ROOT_PATH . 'public' . DS . 'adver');
    //获取图像路径
    $savename=$info->getSaveName();
    $path="/adver/".str_replace('\\','/',$savename);
    //打开需要处理的图像
    $img=\think\Image::open("./public/adver/".$savename);
    //获取文件上传的后缀
    $ext=$info->getExtension();
    $imgname=time().rand(10000,99999);
    //缩放
    $a=$img->thumb(740,400)->save("./adver/s_publicimg/".$imgname.".".$ext);
    // var_dump($a);exit;
    $data['name']=$request->param('name');
    $data['adv_id']=$request->param('id');
    $data['picurl']=$imgname.".".$ext;
    $data['opic']=$path;
    $data['price']=$request->param('price');
    $data['number']=$request->param('number');
    $data['taste']=$request->param('taste');
    $data['content']=$request->param('content');
    
    if(Db::table("adv_pro")->insert($data)){
      $this->success("广告信息添加成功","/adminadver/adver_list");
    }else{
      $this->error("系统繁忙，请稍后重试！","/adminadver/adver_add");
    }
  }

  //版位数据列表
  public function getAdverpro_list($id){
    $a_info=Db::table("adv_pro")->where("adv_id",$id)->select();
    return $this->fetch("Adverpro/adverpro_list",['a_info'=>$a_info]); 
  }

  //版位数据编辑
  public function getAdverpro_edit($id){
    $ad_info=Db::table("adv_pro")->where("id",$id)->find();
    return $this->fetch("Adverpro/adverpro_edit",["ad_info"=>$ad_info]);
  }

  //版位数据执行编辑
  public function postDoadverpro_edit(){
    $request=request();
    $olddata=Db::table("adv_pro")->where("id",$request->param('id'))->find();
    $data=$request->except(['action','id']);
    $file=$request->file('picurl');
    if ($file) {
      //有新的图上传
      $info=$file->move(ROOT_PATH . 'public' . DS . 'adver');
      //获取图像路径
      $savename=$info->getSaveName();
      $path="/adver/".preg_replace("/\\\/", "/", $savename);
      $img=\think\Image::open("./adver/".$savename);
      //获取文件上传的后缀
      $ext=$info->getExtension();
      $imgname=time().rand(10000,99999);
      //缩放
      $img->thumb(100,100)->save("./adver/s_publicimg/".$imgname.".".$ext);
      $data['picurl']=$imgname.".".$ext;
      $data['opic']=$path;
      // var_dump($data);exit;
      $row=Db::table('adv_pro')->where('id',$request->param('id'))->update($data);
      // var_dump($row);
      if ($row) {
          //删除缩放后的图片
         @unlink("./adver/s_publicimg/".$olddata['picurl']);
         //删除原图片
         @unlink(".".$olddata['opic']);
         $this->success("数据修改成功","/adminadverpro/adverpro_list?id=".$olddata['adv_id']);
        }else{
          $this->error("系统繁忙，请稍后重试！","/adminadverpro/adverpro_list?id=".$olddata['adv_id']);
        }
    }else{
      $data['picurl']=$olddata['picurl'];
      $data['opic']=$olddata['opic'];
      $row=Db::table("adv_pro")->where("id",$request->param('id'))->update($data);
      if ($row) {
        $this->success("数据修改成功","/adminadverpro/adverpro_list?id=".$olddata['adv_id']);
      }else{
        $this->error("系统繁忙，请稍后重试！","/adminadverpro/adverpro_list?id=".$olddata['adv_id']);
      }
    }
  }

  //版位数据状态修改 上架
  public function postAdverpro_status_start($id){
    $row=Db::table("adv_pro")->where("id",$id)->update(['status'=>1]);
  }
  //版位数据状态修改 下架
  public function postAdverpro_status_stop($id){
    $row=Db::table("adv_pro")->where("id",$id)->update(['status'=>0]);
  }

  //执行ajax删除
  public function postAdverpro_del($id){
    $opic=Db::table("adv_pro")->where("id",$id)->value("opic");
    $picurl=Db::table("adv_pro")->where("id",$id)->where("picurl");
    @unlink("./adver/s_publicimg/".$picurl);
    @unlink(".".$opic);
    Db::table("adv_pro")->where("id",$id)->delete();
  }


  




}
  