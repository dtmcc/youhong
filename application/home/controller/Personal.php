<?php
namespace app\home\controller;
use think\Db;
use think\Session;
use think\Controller;
use think\Config;
class Personal extends Controller{
	//定义一个生成唯一邀请码的函数
		function createCode($m_id) {
	    static $source_string = 'E5FCDG3HQA4B1NOPIJ2RSTUV67MWX89KLYZ';
	    $num = $m_id;
	    $code = '';
	    while ( $num > 0) {
	        $mod = $num % 35;
	        $num = ($num - $mod) / 35;
	        $code = $source_string[$mod].$code;
	    }
	    if(empty($code[3]))
	        $code = str_pad($code,4,'0',STR_PAD_LEFT);
	    return $code;
		}
	//定义一个解析该唯一邀请码的函数
		function decode($code) {
	    static $source_string = 'E5FCDG3HQA4B1NOPIJ2RSTUV67MWX89KLYZ';
	    if (strrpos($code, '0') !== false)
	        $code = substr($code, strrpos($code, '0')+1);
	    $len = strlen($code);
	    $code = strrev($code);
	    $num = 0;
	    for ($i=0; $i < $len; $i++) {
	        $num += strpos($source_string, $code[$i]) * pow(35, $i);
	    }
	    return $num;
		}
	//-------------------------------会员中心-------------------------//
    

	//个人中心主页
	public function getPersonal(){
		$id=Session::get("id");
		if ($id == null ) {
			return $this->error("亲还未登录，无法进入个人中心哦！","/homelogin/login");
		}
		$m_info=Db::table("member")->where("id",$id)->find();
		$pid=$this->decode($m_info['matt']);
		$p_username=Db::table("member")->where("id",$pid)->value("username");
		$rankarr=['1'=>"青铜会员",'2'=>"白银会员",'3'=>"黄金会员",'4'=>"铂金会员",'5'=>"钻石会员",'6'=>"大师会员",'7'=>"王者会员"];
		return $this->fetch("Personal/personal",['m_info'=>$m_info,'rankarr'=>$rankarr,'p_username'=>$p_username]);
	}
    //商品列表及详情页ajax添加收藏
    public function postCollection(){
      $request=request();
      $data['p_id'] =  $request->param("k");
      $data['m_id'] = Session::get("id");
      if ($data['m_id'] == null ) {echo "3";exit;}
      $row=Db::table("collection")->where("m_id",$data['m_id'])->where("p_id",$data['p_id'])->find();
      if ($row) {
      	echo "2";
      }else{
      	Db::table("collection")->insert($data);
      	echo "1";
      }
    }

    //订单中心-》收获地址
    public function getM_address(){
    	$id=Session::get("id");
    	$data=Db::table("address")->where("m_id",$id)->select();
    	// var_dump($a_data);exit;
    	return $this->fetch("Personal/m_address",['data'=>$data]);
    }
    //收获地址执行添加
    public function postM_address_add(){
    	$request=request();
    	$data=$request->only(['name','phone','adds','zipcode']);
    	$data['m_id']=Session::get("id");
    	$data['area']=$request->param('s_province')." ".$request->param('s_city')." ".$request->param('s_county');
    	$row=Db::table("address")->insert($data);
    	if ($row) {
    		$this->success("收获地址添加成功！","/homepersonal/m_address");
    	}
    }

    //ajax设为默认或删除默认地址
    public function postDel_address_status(){
    	//取消
    	$request=request();
    	$id=$request->param("k");
    	$row=Db::table("address")->where("id",$id)->update(['status'=>0]);
    	if ($row) {
    		echo "1";
    	}
    }

    public function postAdd_address_status(){
    	//设为
    	$request=request();
    	$m_id=Session::get("id");
    	$id=$request->param("k");
    	$row=Db::table("address")->where("m_id",$m_id)->where("status",1)->find();
    	if ($row) {
    		echo "2";
    	}else{
    		Db::table("address")->where("id",$id)->update(['status'=>1]);
    		echo "1";
    	}
    }

    //ajax删除收获地址
    public function postDel_address(){
    	$request=request();
    	$id=$request->param("k");
    	$row=Db::table("address")->where("id",$id)->delete();
    	if ($row) {
    		echo "1";
    	}
    }


    //会员中心-》我的收藏
    public function getM_collection(){
        $id=Session::get("id");
        $c_info=Db::table("collection")->where("m_id",$id)->select();
        foreach ($c_info as $key => $value) {
            $c_info[$key]['p_info']=Db::table("product")->where("id",$value['p_id'])->find();
        }
        // var_dump($c_info);exit;
        return $this->fetch("Personal/m_collection",['c_info'=>$c_info]);
    }

    //会员中心-》我的收藏-》加入购物车
    public function postJ_car(){
        $request=request();
        $data['p_id']=$request->param("k");
        $data['m_id']=Session::get("id");
        Db::table("collection")->where("m_id",$data['m_id'])->where("p_id",$data['p_id'])->delete();
        $row=Db::table("car")->where("m_id",$data['m_id'])->where("p_id",$data['p_id'])->find();
        if ($row) {
            echo "2";
        }else{
            Db::table("car")->insert($data);
            echo "1";
        }
    }

    //会员中心-》我的收藏-》取消收藏
    public function postD_coll(){
        $request=request();
        $data['p_id']=$request->param("k");
        $data['m_id']=Session::get("id");
        $row=Db::table("collection")->where("m_id",$data['m_id'])->where("p_id",$data['p_id'])->delete();
        if ($row) {
            echo "1";
        }
    }

    //会员中心-》我的足迹
    public function getM_footprint(){
        $id=Session::get("id");
        $f_info=Db::table("footprint")->where("m_id",$id)->select();
        foreach ($f_info as $key => $value) {
            $f_info[$key]['p_info']=Db::table("product")->where("id",$value['p_id'])->find();
        }
        return $this->fetch("Personal/m_footprint",['f_info'=>$f_info]);
    }
    //会员中心-》我的足迹-》删除足迹
    public function postD_foot(){
        $request=request();
        $data['p_id']=$request->param("k");
        $data['m_id']=Session::get("id");
        $row=Db::table("footprint")->where("m_id",$data['m_id'])->where("p_id",$data['p_id'])->delete();
        if ($row) {
            echo "1";
        }
    }

    // 会员中心 -》我的留言
    public function getM_msg(){
        $id=Session::get("id");
        $mes_info=Db::table("message")->where("m_id",$id)->select();
        $statusarr=['0'=>"新留言",'1'=>"已回复"];
        return $this->fetch("Personal/m_msg",["mes_info"=>$mes_info,"statusarr"=>$statusarr]);
    }

    // 会员中心 -》提交留言
    public function postDomessage_add(){
        $request=request();
        $file=$request->file('pic');
        if ($file) {
            //移动文件到指定目录
            $info=$file->move(ROOT_PATH . 'public' . DS . 'message');
            //获取图像路径
            $savename=$info->getSaveName();
            $path="/message/".str_replace('\\','/',$savename);
            $data['pic']=$path;
        }
        //打开需要处理的图像
        // $img=\think\Image::open("./message/".$savename);
        //获取文件上传的后缀
        // $ext=$info->getExtension();
        // $imgname=time().rand(10000,99999);
        //缩放
        // $a=$img->thumb(100,100)->save("./message/s_publicimg/".$imgname.".".$ext);
        // var_dump($a);exit;
        $data['m_id']=Session::get("id");
        $data['type']=$request->param('type');
        $data['theme']=$request->param('theme');
        $data['content']=$request->param('content');
        if(Db::table("message")->insert($data)){
          $this->success("留言成功，网站客服会尽快回复您！","/homepersonal/m_msg");
        }else{
          $this->error("系统繁忙，请稍后重试！","/homepersonal/m_msg");
        }
    }

    //ajax查看留言回复
    public function postLook_replay($id){
        $replay=Db::table("message")->where("id",$id)->value("replay");
        echo $replay."\n\n如有更多疑问，请加网站客服QQ：1062436470";
    }

    //我的评论
    
    


    //---------------------------------订单中心--------------------------//
    

    //订单中心  -》我的订单
    public function getm_order(){
        $request=request();
        $o_id=$request->param("id");
        if ($o_id == null) {
            $id=Session::get("id");
            $o_info=Db::table("order")->where("m_id",$id)->select();
            $statusarr=['0'=>"待付款",'1'=>"待发货",'2'=>"待收获",'3'=>"待评价",'4'=>"已完成"];
            return $this->fetch("Personal/m_order",["statusarr"=>$statusarr,'o_info'=>$o_info]);
        }else{
            $id=Session::get("id");
            $o_info=Db::table("order")->where("m_id",$id)->select();
            $d_info=Db::table("detail")->where("o_id",$o_id)->select();
            foreach ($d_info as $key => $value) {
                $d_info[$key]['p_info']=Db::table("product")->where("id",$value['p_id'])->find();
                if ($value['p_id'] === 0) {
                    $d_info[$key]['p_info']=Db::table("adv_pro")->where("id",$value['a_id'])->find();
                }
            }
            $statusarr=['0'=>"待付款",'1'=>"待发货",'2'=>"待收获",'3'=>"待评价",'4'=>"已完成"];            
            // echo "<pre>";
            // var_dump($d_info);exit;
            return $this->fetch("Personal/m_order",["statusarr"=>$statusarr,'o_info'=>$o_info,'d_info'=>$d_info]);
        } 
    }

    //订单中心-》我的订单-》订单详情 -》 去评价
    public function getComment_add(){
        $request=request();
        $d_id=$request->param("id");
        $d_info=DB::table("detail")->where("id",$d_id)->find();
        $d_info['p_info']=Db::table("product")->where("id",$d_info['p_id'])->find();
         $d_info['source']=1;
        if ($d_info['p_id'] === 0) {
            $d_info['p_info']=Db::table("adv_pro")->where("id",$d_info['a_id'])->find();
            $d_info['source']=0;
        }
        return $this->fetch("Personal/m_comment_add",['d_info'=>$d_info]);
    }
    //订单中心-》 我的订单 ->订单详情-》执行评价
    public function postDocomment_add(){
        $request=request();
        $d_id=$request->param("d_id");
        $data=$request->only(['grade','text','d_id']);
        if ($request->param("source") == 1) {
            $data['p_id']=$request->param("p_id");
        }else{
            $data['a_id']=$request->param("p_id");
        }
        $data['o_id']=Db::table("detail")->where("id",$d_id)->value("o_id");
        $data['m_id']=Session::get("id");
        $data['addtime']=time();
        $row=Db::table("comment")->insert($data);
        if ($row) {
            Db::table("detail")->where("id",$d_id)->update(['status'=>1]);
            $this->success("评价完成，感谢您的参与！","/homepersonal/m_comment");
        }else{
            $this->error("系统繁忙，请稍后重试！","/homepersonal/m_comment");
        }
    }
    //会员中心 -》我的评论
    public function getM_comment(){
        $id=Session::get("id");
        $com_info=Db::table("comment")->where("m_id",$id)->select();
        foreach ($com_info as $key => $value) {
            $com_info[$key]['p_info']=Db::table("product")->where("id",$value['p_id'])->find();
            if ($value['p_id']===0) {
                $com_info[$key]['p_info']=Db::table("adv_pro")->where("id",$value['a_id'])->find();
            }
            $com_info[$key]['o_code']=Db::table("order")->where("id",$value['o_id'])->value("order_code");
        }
        // echo "<pre>";    
        // var_dump($com_info);exit;
        return $this->fetch("Personal/m_comment",['com_info'=>$com_info]);
    }

    //订单中心 -》 我的订单  ajax确认收货
    public function postOrder_status($id){
        Db::table("order")->where("id",$id)->update(['status'=>3]);
    }


    //-------------------------会员中心-------------------------------//
    

    //会员中心  -》 用户信息
    public  function getM_user(){
        $id=Session::get("id");
        $m_info=Db::table("member")->where("id",$id)->find();
        $pid=$this->decode($m_info['matt']);
        $p_username=Db::table("member")->where("id",$pid)->value("username");
        $rankarr=['1'=>"青铜会员",'2'=>"白银会员",'3'=>"黄金会员",'4'=>"铂金会员",'5'=>"钻石会员",'6'=>"大师会员",'7'=>"王者会员"];
        return $this->fetch("Personal/m_user",['m_info'=>$m_info,'rankarr'=>$rankarr,'p_username'=>$p_username]);
    }
    //会员中心 点击查询邀请人信息
    public function postQuery_matt($username){
        $m_info['email']=Db::table("member")->where("username",$username)->value(['email']);
        $m_info['phone']=Db::table("member")->where("username",$username)->value(['phone']);
        echo json_encode($m_info);
    }


    //-----------------------账户中心----------------------//
    

    //账户安全
    public function getM_safe(){
        $id=Session::get("id");
        $m_info=Db::table("member")->where("id",$id)->find();
        return $this->fetch("Personal/m_safe",['m_info'=>$m_info]);
    }

    //账户安全-》信息修改（手机号）
    public function postChangephone(){
        $request=request();
        $id=Session::get("id");
        $row=Db::table("member")->where("id",$id)->update(['phone'=>$request->param('phone')]);
        if ($row) {
            $this->success("手机号码修改成功","/homepersonal/m_safe");
        }
    }
    //账户安全-》信息修改（邮箱）
    public function postChangeemail(){
        $request=request();
        $id=Session::get("id");
        $row=Db::table("member")->where("email",$request->param('email'))->find();
        if ($row) {
            $this->error("邮箱已被暂用，修改失败！","/homepersonal/m_safe");
        }else{
            $row1=Db::table("member")->where("id",$id)->update(['email'=>$request->param('email')]);
            if ($row1) {
                $this->success("邮箱修改成功","/homepersonal/m_safe");
            }else{
                $this->error("系统繁忙，请稍后重试！","/homepersonal/m_safe");
            }   
        }
    }
    //检测原密码是否正确
    public function postCheckpwd(){
        $request=request();
        $pwd=$request->param("pwd");
        $id=Session::get("id");
        $password=Db::table("member")->where("id",$id)->value("password");
        if ($password  == $pwd) {echo "1";}
    }

    //账户安全-》信息修改（密码）
    public function postChangepwd(){
        $request=request();
        if ($request->param("password") !== $request->param("repassword")) {
            $this->error("两次密码输入不一致！","/homepersonal/m_safe");
        }
        $id=Session::get("id");
        $row=Db::table("member")->where("id",$id)->update(['password'=>$request->param('password')]);
        if ($row) {
            $this->success("密码修改成功","/homepersonal/m_safe");
        }
    }



    //------------------------分销中心----------------------//
    

    //我的会员
    public function getM_member(){
        $request=request();
        $list=$request->param("list");
        $id=Session::get("id");
        $listarr=['1'=>"一",'2'=>"二",'3'=>"三"];
        $rankarr=['1'=>"青铜会员",'2'=>"白银会员",'3'=>"黄金会员",'4'=>"铂金会员",'5'=>"钻石会员",'6'=>"大师会员",'7'=>"王者会员"];
        //我的一级会员
        $fir_m_info=Db::table("member")->where("pid",$id)->select();
        $numfir=count($fir_m_info);
        //我的二级会员
        $sec_m_info=Db::table("member")->where("gid",$id)->select();
        $numsec=count($sec_m_info);
        //我的三级会员
        $thr_m_info=Db::table("member")->where("pgid",$id)->select();
        $numthr=count($thr_m_info);

        //总会员数
        $numtotal=$numfir+$numsec+$numthr;
        // var_dump($fir_m_info);exit;
        return $this->fetch("Personal/m_member",['numtotal'=>$numtotal,'numfir'=>$numfir,'numsec'=>$numsec,'numthr'=>$numthr,'fir_m_info'=>$fir_m_info,'sec_m_info'=>$sec_m_info,'thr_m_info'=>$thr_m_info,'list'=>$list,'listarr'=>$listarr,'rankarr'=>$rankarr]);   
    }
   
}

?>
