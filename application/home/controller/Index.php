<?php
namespace app\home\controller;
use think\Db;
use think\Session;
use think\Controller;
use think\Config;
class Index extends Controller{
	// 无线递归分类获取
	public function getcategorybypid($pid){
		$datainfo=Db::table("category")->where("pid",$pid)->select();
		$data=[];
		foreach($datainfo as $key=>$value){
			// 查询根据父类的id等于子类的pid
			$value['shop']=$this->getcategorybypid($value['id']);
			$data[]=$value;
		}
		return $data;
	}

	
  	//定义一个获取该类别下所有子类别的方法
  	public function getallcategory($id){
      $cate=$this->getcategorybypid($id);
      $data2=[];
        foreach ($cate as $key => $value) {
          $value['shop']=$this->getcategorybypid($value['id']);
          foreach ($value['shop'] as $key => $value2) {
            $data2[]=$value2['id'];
          }
          $data2[]=$value['id'];
        }
      $a=implode(",",$data2);
      $arr=$id.",".$a;
      return $arr;
      // $shops=Db::table("admin_goods")->where("cates_id",'in',$arr)->select();
      // return $shops;
  	}

  	

	//加载首页
	public function getIndex(){
		//获取所有的分类
		$categorys=$this->getcategorybypid(0);
		//获取首页的banner数据
		$ban_info=Db::table("adv_pro")->where("adv_id",1)->where("status",1)->select();
		//获取首页的top数据
		$top_info=Db::table("adv_pro")->where("adv_id",5)->where("status",1)->find();
		//获取购物车的数据
		$car_info=Db::table("car")->where("m_id",session::get("id"))->select();
		$tot="";
		foreach ($car_info as $key => $value) {
			$car_info[$key]['p_info']=Db::table("product")->where("id",$value['p_id'])->find();
			$car_info[$key]['total']=$car_info[$key]['p_info']['price']*$car_info[$key]['num'];
			$tot+=$car_info[$key]['total'];
            if ($value['p_id'] === 0) {
                $car_info[$key]['p_info']=Db::table("adv_pro")->where("id",$value['a_id'])->find();
                $car_info[$key]['total']=$car_info[$key]['p_info']['price']*$car_info[$key]['num'];
                $tot+=$car_info[$key]['total'];
            }
		}
		// echo "<pre>";
		// echo $tot;
		// var_dump($car_info);exit;
	  	return $this->fetch("Home/index",['categorys'=>$categorys,'ban_info'=>$ban_info,'top_info'=>$top_info,'car_info'=>$car_info,'tot'=>$tot]);
	}
}

?>
