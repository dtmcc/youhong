<?php
namespace app\admin\controller;
use think\Db;
use think\Session;
use think\Controller;
use think\Config;
class Order extends Controller{
  //加载订单列表页
  public function getOrder_list(){
    $o_info=Db::table("order")->select();
    foreach ($o_info as $key => $value) {
      $o_info[$key]['address']=Db::table("address")->where("id",$value['address_id'])->find();
    }
    $statusarr=['0'=>"已下单，待付款",'1'=>"已付款，待发货",'2'=>"已发货，待收获",'3'=>"已收货，待评价",'4'=>"已评价，完成"];
  	return $this->fetch("Order/order_list",['o_info'=>$o_info,'statusarr'=>$statusarr]);
  }

  //编辑订单
  public function getOrder_edit(){
    $request=request();
    $id=$request->param("id");
    $o_info=Db::table("order")->where("id",$id)->find();
    $o_info['address']=Db::table("address")->where("id",$o_info['address_id'])->find();
    return $this->fetch("Order/order_edit",['o_info'=>$o_info]);
  }

  //执行编辑订单
  public function postDoorder_edit(){
    $request=request();
    $total=$request->param("total");
    $id=$request->param("id");
    $newadd = $request->only(['name','phone','area','adds','m_id']);
    $new_a_id=Db::name('address')->insertGetId($newadd);
    $data['address_id']=$new_a_id;
    $data['total']=$total;
    $row=Db::table("order")->where("id",$id)->update($data);
    if ($row) {
      $this->success("修改成功！","/adminorder/Order_list");
    }else{
      $this->error("修改失败！","/adminorder/Order_list");
    } 
  }

  //查看订单详情
  public function getOrder_details(){
    $request=request();
    $o_id=$request->param("o_id");
    $d_info=Db::table("detail")->where("o_id",$o_id)->select();
    foreach ($d_info as $key => $value) {
      $d_info[$key]['p_info']=Db::table("product")->where("id",$value['p_id'])->find();
    }
    foreach ($d_info as $key => $value) {
      $d_info[$key]['o_info']=Db::table("order")->where("id",$value['o_id'])->find();
    }
    // var_dump($d_info);exit;
    return $this->fetch("Order/order_details",['d_info'=>$d_info]);
  }

  

 
}

?>
