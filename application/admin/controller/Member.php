<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
//导入Config.php
use think\Config;
class Member extends Controller{

  //加载会员列表页
  public function getMember_list(){
  	$request=request();
    $k=$request->get('keywords');
  	$data=Db::table("member")->where('username','like',"%".$k."%")->paginate(5);
  	$sexarr=['0'=>"保密",'1'=>"男",'2'=>"女"];
  	$statusarr=['0'=>"禁用用户",'1'=>"正常用户",'2'=>"管理员用户"];
	  $rankarr=['1'=>"青铜会员",'2'=>"白银会员",'3'=>"黄金会员",'4'=>"铂金会员",'5'=>"钻石会员",'6'=>"大师会员",'7'=>"王者会员"];
  	// var_dump($data);exit;
  	return $this->fetch("Member/member_list",['sexarr'=>$sexarr,'rankarr'=>$rankarr,'statusarr'=>$statusarr,'data'=>$data,'request'=>$request->param(),'k'=>$k]);
  }

  //加载被封禁会员
  public function getMember_del(){
  	return $this->fetch("Member/member_del");
  }

  //加载会员编辑修改页面
  public function getMember_edit($id){
  	$info=Db::table("member")->where("id",$id)->find();
    return $this->fetch("/Member/member_edit",['info'=>$info]);
  }
  //执行会员编辑修改
  public function postDomember_edit(){
  	$request=request();
  	$id=$request->param("id");
  	$info=$request->except(['id','action']);
  	$row=Db::table("member")->where("id",$id)->update($info);
  	if ($row) {
  		$this->success("信息修改成功","/adminmember/member_list");
  	}else{
  		$this->error("信息修改失败","/adminmember/member_edit");
  	}
  }

  //加载会员添加页面
  public function getMember_add(){
  	return $this->fetch("Member/member_add");
  }
  //执行会员添加
  public function postDomember_add(){
    $request=request();
    $data=$request->except(['action']);
    $data['sex']=0;
    $data['face']="/static/uploads/user.jpg";
    $data['jointime']=time();
    $row=Db::table("member")->insert($data);
    if($row){
      $this->success("添加成功","/adminmember/member_list");
    }else{
      $this->success("添加失败","/adminmember/member_add");
    } 
  }

  //加载会员添加页面 弹窗式
  public function getMember_add_t(){
  	return $this->fetch("Member/member_add_t");
  }
}

?>
