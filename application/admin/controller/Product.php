<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
use think\Config;
class Product extends Controller{
  //定义一个默认自动获取该分类下的所有子分类的方法
  public function getAllcates(){
    // concat拼接字符串，paths起别名 order by根据顺序查找字段
    $data=Db::query('select *,concat(path,",",id) as paths from category order by paths');
    foreach($data as $key=>$value){
      // 统计，在 path 字符串中出现的个数;
      $length=substr_count($value['path'],',');
      // str_repeat 重复替换字符串
      $data[$key]['name']=str_repeat("　", $length).str_repeat("|----", $length).$value['name'];
    }
    return $data;   
  }

  //加载商品添加的页面
  public function getProduct_add(){
    $allcates=$this->getAllcates();
    return $this->fetch("Product/product_add",['allcates'=>$allcates]);
  }
  //执行商品的添加
  public function postDoproduct_add(){
    $request=request();
    $file=$request->file('picurl');
    //移动文件到指定目录
    $info=$file->move(ROOT_PATH . 'public' . DS . 'uploads');
    //获取图像路径
    $savename=$info->getSaveName();
    $path="/uploads/".str_replace('\\','/',$savename);
    //打开需要处理的图像
    $img=\think\Image::open("./uploads/".$savename);
    //获取文件上传的后缀
    $ext=$info->getExtension();
    $imgname=time().rand(10000,99999);
    //缩放
    $a=$img->thumb(100,100)->save("./uploads/s_publicimg/".$imgname.".".$ext);
    // var_dump($a);exit;
    $data['name']=$request->param('name');
    $data['cates_id']=$request->param('cates_id');
    $data['picurl']=$imgname.".".$ext;
    $data['opic']=$path;
    $data['price']=$request->param('price');
    $data['number']=$request->param('number');
    $data['taste']=$request->param('taste');
    $data['content']=$request->param('content');
    if(Db::table("product")->insert($data)){
      $this->success("商品添加成功","/adminproduct/product_list");
    }else{
      $this->error("商品添加失败","/adminproduct/product_add");
    }
  } 

  //加载商品列表页面
  public function getProduct_list(){
    $data=Db::table("product")->select();
    // var_dump($data);exit;
    foreach ($data as $key => $value) {
      $name[$key]=$value['cates_id'];
      $data[$key]['cates_id']=Db::table("category")->where("id",$name[$key])->find();
    }
    // var_dump($data);exit;
    return $this->fetch("Product/product_list",['data'=>$data]);
  }

  //执行商品上架的ajax的修改
  public function postProduct_status_start($id){
    $row=Db::table("product")->where("id",$id)->update(['status'=>1]);
  }
  //执行商品下架的ajax的修改
  public function postProduct_status_stop($id){
    $row=Db::table("product")->where("id",$id)->update(['status'=>0]);
  }

  //加载商品的编辑模板
  public function getProduct_edit(){
    $request=request();
    $info=Db::table("product")->where("id",$request->param("id"))->find();
    return $this->fetch("Product/product_edit",['info'=>$info]); 
  }

  //执行商品的修改操作
  public function postDoproduct_edit(){
    $request=request();
    $olddata=Db::table("product")->where("id",$request->param('id'))->find();
    $data=$request->except(['action','id']);
    $file=$request->file('picurl');
    if ($file) {
      //有新的图上传
      $info=$file->move(ROOT_PATH . 'public' . DS . 'uploads');
      //获取图像路径
      $savename=$info->getSaveName();
      $path="/uploads/".preg_replace("/\\\/", "/", $savename);
      $img=\think\Image::open("./uploads/".$savename);
      //获取文件上传的后缀
      $ext=$info->getExtension();
      $imgname=time().rand(10000,99999);
      //缩放
      $img->thumb(100,100)->save("./uploads/s_publicimg/".$imgname.".".$ext);
      $data['picurl']=$imgname.".".$ext;
      $data['opic']=$path;
      // var_dump($data);exit;
      $row=Db::table('product')->where('id',$request->param('id'))->update($data);
      // var_dump($row);
      if ($row) {
          //删除缩放后的图片
         @unlink("./uploads/s_publicimg/".$olddata['picurl']);
         //删除原图片
         @unlink(".".$olddata['opic']);
         $this->success("修改成功","/adminproduct/product_list");
        }else{
          $this->error("修改失败","/adminproduct/product_edit?id=".$request->param('id'));
        }
    }else{
      $data['picurl']=$olddata['picurl'];
      $data['opic']=$olddata['opic'];
      $row=Db::table("product")->where("id",$request->param('id'))->update($data);
      if ($row) {
        $this->success("数据修改成功","/adminproduct/product_list");
      }else{
        $this->error("数据修改失败，请重新修改","/adminproduct/product_edit?id=".$request->param('id'));
      }
    }
  }

  //执行ajax删除
  public function postProduct_del($id){
    $opic=Db::table("product")->where("id",$id)->value("opic");
    @unlink(".".$opic);
    Db::table("product")->where("id",$id)->delete();
  }

}
  