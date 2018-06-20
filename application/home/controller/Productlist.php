<?php
namespace app\home\controller;
use think\Db;
use think\Session;
use think\Controller;
use think\Config;
class Productlist extends Controller{
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
  	//定义一个获取该类别下所有子类别的（或商品）方法
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


  	//加载商品列表页
  	public function getProductlist(){
  		$request=request();
  		$id=$request->param("id");
      $k=$request->get("key");
      $arr=$this->getallcategory($id);
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
      // if (!$request->isAjax()) {
      if ($k) {
        $productinfo=Db::table("product")->where('name','like',"%".$k."%")->select();
      }else{
        $productinfo=Db::table("product")->where("cates_id","in",$arr)->select();
      }
      // echo $k;exit;
        return $this->fetch("Productlist/productlist",["productinfo"=>$productinfo,'car_info'=>$car_info,'tot'=>$tot,'key'=>$k]);
      // }
  	}

    //加入购物车
    public function postJ_car(){
      $request=request();
      $data['p_id']=$request->param("k");
      $data['m_id']=Session::get("id");
      if ($data['m_id'] == null ) {echo "3";exit;}
      $row=Db::table("car")->where("m_id",$data['m_id'])->where("p_id",$data['p_id'])->find();
      if ($row) {
        echo "2";
      }else{
        Db::table("car")->insert($data);
        echo "1";
      } 
    }

}

?>
