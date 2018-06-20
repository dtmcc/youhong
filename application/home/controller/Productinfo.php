<?php
namespace app\home\controller;
use think\Db;
use think\Session;
use think\Controller;
use think\Config;
class Productinfo extends Controller{
    
    //列表
    public function getProductinfo(){
      	$request=request();
        $id=Session::get("id");
      	$p_id=$request->param("id");
        if ($p_id) {
          $p_info=Db::table("product")->where("id",$p_id)->find();
          $p_info['source']="1";
          $foot['m_id']=$id;
          $foot['p_id']=$p_id;
          $foot['addtime']=time();
          $row=Db::table("footprint")->where("m_id",$id)->where("p_id",$p_id)->find();
          if($row){
            Db::table("footprint")->where("m_id",$id)->where("p_id",$p_id)->update(['addtime'=>$foot['addtime']]);
          }else{
            Db::table("footprint")->insert($foot);
          }
        }else{
          $p_id=$request->param("aid");
          $p_info=Db::table("adv_pro")->where("id",$p_id)->find();
          $p_info['source']="0";
        }

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
      	return $this->fetch("Productinfo/productinfo",["p_info"=>$p_info,"car_info"=>$car_info,"tot"=>$tot]);
    }

    //加入购物车
    public function postJ_car(){
      $request=request();
      $source=$request->param("source");
      if ($source == "1") {
        $p_id=$request->param("p_id");
        $data['p_id']=$request->param("p_id");
      }else{
        $p_id=$request->param("p_id");
        $data['a_id']=$request->param("p_id");
      }
      $data['num']=$request->param("num");
      $data['m_id']=Session::get("id");
      if ($data['m_id'] == null ) {echo "3";exit;}
      $row1=Db::table("car")->where("m_id",$data['m_id'])->where("p_id",$p_id)->find();
      $row2=Db::table("car")->where("m_id",$data['m_id'])->where("a_id",$p_id)->find();
      if ($row1 or $row2) {
        echo "2";
      }else{
        Db::table("car")->insert($data);
        echo "1";
      } 
    }
}

?>
