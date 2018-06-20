<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
//导入Config.php
use think\Config;
class Category extends Controller{

  public function getCates(){
    // concat拼接字符串，paths起别名 order by根据顺序查找字段
    $data=Db::query('select *,concat(path,",",id) as paths from cates order by paths');
    // echo "<pre>";
    // var_dump($data);exit;
    foreach($data as $key=>$value){
      // echo $value['path']."<br>";
      // 统计，在 path 字符串中出现的个数;
      $length=substr_count($value['path'],',');
      // echo $length."<br>";
      // str_repeat 重复替换字符串
      $data[$key]['name']=str_repeat("　", $length).str_repeat("|----", $length).$value['name'];
    }
    return $data;   
  }

  

  //加载顶级类别添加模块
  public function getCategory_add(){
  	return $this->fetch("Category/category_add");
  }

  //执行顶级类别的添加
  public function postDocategory_add(){
  	$request=request();
  	$data=$request->except(['action']);
  	$data['pid']=0;
  	$data['path']="0";
  	$row=Db::table("category")->insert($data);
  	if ($row) {
  		$this->success("添加成功","/admincategory/category_list");
  	}else{
  		$this->error("添加失败","/admincategory/category_add");
  	}
  }

  //加载添加带有父类别的子类别模板
  public function getCategory_add_f(){
  	$request=request();
  	$data=Db::table("category")->where("id",$request->param('id'))->find();
  	return $this->fetch("Category/category_add_f",['data'=>$data]);	
  }
  //执行添加带有父级类别的子级类别
  public function postDocategory_add_f(){
  	$request=request();
  	//获取父类的信息
  	$finfo=Db::table("category")->where("id",$request->param('pid'))->find();
  	$info['name']=$request->param('name');
  	$info['pid']=$request->param('pid');
  	$info['path']=$finfo['path'].",".$finfo['id'];
  	$row=Db::table("category")->insert($info);
  	if ($row) {
  		$this->success("添加成功","/admincategory/category_list");
  	}else{
  		$this->error("添加失败","/admincategory/category_list");
  	}
  }

  //加载类别的编辑页面
 	public function getCategory_edit(){
 		$request=request();
 		$id=$request->param("id");
 		$info=Db::table("category")->where("id",$id)->find();
 		return $this->fetch("Category/category_edit",['info'=>$info]);
 	}
 	//执行类别修改
 	public function postDocategory_edit(){
 		$request=request();
 		$row=Db::table("category")->where("id",$request->param("id"))->update(['name'=>$request->param("name")]);
 		if ($row) {
 			$this->success("修改成功","/admincategory/category_list");
 		}else{
 			$this->error("修改失败","/admincategory/category_list");
 		}
 	}
  

  //加载类别的列表模块
  public function getCategory_list(){
  	$catesarr=['0'=>'顶级分类','1'=>'一级分类','2'=>'二级分类','3'=>'3级分类'];
  	$data=Db::query('select *,concat(path,",",id) as paths from category order by paths');
  	foreach($data as $key=>$value){
      $length=substr_count($value['path'],',');
      $data[$key]['name']=str_repeat("　", $length).str_repeat("|----", $length).$value['name'];
      $data[$key]['length']=$length;
    }
    // var_dump($data);exit;
  	return $this->fetch("Category/category_list",['data'=>$data,'catesarr'=>$catesarr]);
  }

  //执行类别的ajax删除
  public function postCategory_del(){
    $request=request();
    $row=Db::table("category")->where('pid',$request->param('id'))->find();
    if ($row) {
      echo "1";
    }else{
      Db::table("category")->where('id',$request->param('id'))->delete();
    }
  }
}
  