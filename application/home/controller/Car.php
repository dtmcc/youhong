<?php
namespace app\home\controller;
use think\Db;
use think\Session;
use think\Controller;
use think\Config;
class Car extends Controller{
    //购物车列表
    public function getCar_list(){
        $id=Session::get('id');
        $c_info=Db::table("car")->where("m_id",$id)->select();
        foreach ($c_info as $key => $value) {
            $c_info[$key]['p_info']=Db::table("product")->where("id",$value['p_id'])->find();
            $c_info[$key]['source']="1";
            if ($value['p_id'] === 0) {
                $c_info[$key]['p_info']=Db::table("adv_pro")->where("id",$value['a_id'])->find();
                $c_info[$key]['source']="0";
            }
        }
        // echo "<pre>";
        // var_dump($c_info);exit;
        return $this->fetch("Car/car_one",['c_info'=>$c_info]);
    }

    // ----------------------------购物车页面的ajax判断---------------------------------------//
    //购物车，减  red
    public function postCar_red(){
        $request=request();
        $source=$request->param("source");
        if ($source == "1") {
            $id=$request->param("id");
            $c_info=Db::table("car")->where("id",$id)->find();
            $c_info['num']-=1;
            if($c_info['num']<1){
                $c_info['num']=1;
                $data['error']="3";
            }else{
                $data['error']="";
            }
            Db::table("car")->where("id",$id)->update(["num"=>$c_info['num']]);
            $new_c_info=Db::table("car")->where("id",$id)->find();
            $data['num']=$new_c_info['num'];
            $data['tot']=$new_c_info['num']*Db::table("product")->where("id",$new_c_info['p_id'])->value("price");
            $data['scores']=$data['tot']*0.1;
            // 数组需要转换成json数据
            echo json_encode($data);
        }else{
            $id=$request->param("id");
            $c_info=Db::table("car")->where("id",$id)->find();
            $c_info['num']-=1;
            if($c_info['num']<1){
                $c_info['num']=1;
                $data['error']="3";
            }else{
                $data['error']="";
            }
            Db::table("car")->where("id",$id)->update(["num"=>$c_info['num']]);
            $new_c_info=Db::table("car")->where("id",$id)->find();
            $data['num']=$new_c_info['num'];
            $data['tot']=$new_c_info['num']*Db::table("adv_pro")->where("id",$new_c_info['a_id'])->value("price");
            $data['scores']=$data['tot']*0.1;
            // 数组需要转换成json数据
            echo json_encode($data);
        }
        
    }

    //购物车  加 add
    public function postCar_add(){
        $request=request();
        $source=$request->param("source");
        if ($source == "1") {
            //正规来源product
            $id=$request->param("id");
            $c_info=Db::table("car")->where("id",$id)->find();
            $old_c_info_num=Db::table("product")->where("id",$c_info['p_id'])->value("number");
            $c_info['num']+=1;
            if($c_info['num']>$old_c_info_num){
                $c_info['num']=$old_c_info_num;
                $data['error']="3";
            }else{
                $data['error']="";
            }
            Db::table("car")->where("id",$id)->update(["num"=>$c_info['num']]);
            $new_c_info=Db::table("car")->where("id",$id)->find();
            $data['num']=$new_c_info['num'];
            $data['tot']=$new_c_info['num']*Db::table("product")->where("id",$new_c_info['p_id'])->value("price");
            $data['scores']=$data['tot']*0.1;
            // 数组需要转换成json数据
            echo json_encode($data);
        }else{
            $id=$request->param("id");
            $c_info=Db::table("car")->where("id",$id)->find();
            $old_c_info_num=Db::table("adv_pro")->where("id",$c_info['a_id'])->value("number");
            $c_info['num']+=1;
            if($c_info['num']>$old_c_info_num){
                $c_info['num']=$old_c_info_num;
                $data['error']="3";
            }else{
                $data['error']="";
            }
            Db::table("car")->where("id",$id)->update(["num"=>$c_info['num']]);
            $new_c_info=Db::table("car")->where("id",$id)->find();
            $data['num']=$new_c_info['num'];
            $data['tot']=$new_c_info['num']*Db::table("adv_pro")->where("id",$new_c_info['a_id'])->value("price");
            $data['scores']=$data['tot']*0.1;
            // 数组需要转换成json数据
            echo json_encode($data);
        }
        
    }


    //购物车提交结算
    public function postCar_two(){
        $request=request();
        $id=Session::get("id");
        $total=$request->param('total');
        $c_ids=$request->only('c_id');
        foreach ($c_ids as $key => $value) {
           $c_id=implode(',',$value);
        }

        // $p_ids=$request->only('p_id');
        // foreach ($p_ids as $key => $value) {
        //    $p_id=implode(',',$value);
        // }
        
        //购物车id号拼接  存入session中
        Session::set("c_id",$c_id);
        //购物车中的商品id号拼接  存入session中
        // Session::set("p_id",$p_id);
        // echo Session::get("p_id");
        
        $c_info=Db::table("car")->where("id","in",$c_id)->select();
        foreach ($c_info as $k => $value) {
            $c_info[$k]['p_info']=Db::table("product")->where("id",$value['p_id'])->find();
            if ($value['p_id'] === 0) {
                $c_info[$k]['p_info']=Db::table("adv_pro")->where("id",$value['a_id'])->find();
            }
        }
        // echo "<pre>";
        // var_dump($c_info);exit;
        //该用户收获地址信息
        $m_address=Db::table("address")->where("m_id",$id)->where("status",1)->find();
        return $this->fetch("Car/car_two",['m_address'=>$m_address,'c_info'=>$c_info,'total'=>$total]);
    }

    //购物车结算页面
    public function postCar_three(){
        $request=request();
        $data = $request->except('action');
        $data['m_id']=Session::get("id");
        // $data['p_id']=Session::get("p_id");
        $data['order_code']=rand(10000,99999).time().rand(10000,99999);
        $data['ordertime']=time();
        $data['status']=0;
        $o_id=Db::table("order")->insertGetId($data);
        //获取提交的购物车id号
        $c_id=Session::get('c_id');
        $c_info=Db::table("car")->where('id','in',$c_id)->select();
        foreach ($c_info as $key => $value) {
            $arr['o_id']=$o_id;
            $arr['p_id']=$value['p_id'];
            if ($arr['p_id'] === 0 ) {
                $arr['a_id']=$value['a_id'];
            }
            $arr['num']=$value['num'];
            Db::table("detail")->insert($arr);
            Session::delete("c_id");
            Session::delete("p_id");
        }
        Db::table("car")->where("m_id",Session::get("id"))->where('id','in',$c_id)->delete();
        if ($o_id) {
            $this->success("订单提交成功,正在加载支付页面，请稍后！","/homecar/pay/o_id/$o_id");
        }else{
            $this->error("订单提交失败，请重新尝试","/homecar/car_list");
        }
    }

    //支付宝订单接口
   public function getPay($o_id){
    $info=Db::table("order")->where("id",$o_id)->find();
    $params['subject']='尤洪商城购物消费';
    $params['total_amount']=$info['total'];
    $params['out_trade_no']=$info['order_code'];
    \alipay\Pagepay::pay($params,"http://www.youhong.com/homecar/payok");
   }

   //订单付款OK 修改订单状态
    public function getPayok(){
    $order_code=$_GET['out_trade_no'];    
    $data['status']= 1;
    $info=Db::table("order")->where("order_code",$order_code)->update($data);
        if ($info) {
            $this->success("付款成功","/homecar/payover/order_code/{$order_code}");
        }else{
            $this->error("付款失败，请检查订单，如有疑问联系网站客服","/homecar/order_list");
        }
    }

    //修改订单状态后加载付款成功页面
    public function getPayover($order_code){
    // $order_code="62445151624027091925";
    $o_info=Db::table("order")->where("order_code",$order_code)->find();
    $address=Db::table("address")->where("id",$o_info['address_id'])->find();
    // var_dump($o_info);var_dump($address);exit;
    return $this->fetch("Car/car_three",['o_info'=>$o_info,'address'=>$address]);
  }
}

?>
