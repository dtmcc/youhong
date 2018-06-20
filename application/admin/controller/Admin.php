<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
//导入Config.php
use think\Config;
class Admin extends Controller{
  //加载管理员列表页
  public function getAdmin_list(){
    $data=Db::table("member")->where('status',2)->select();
    
    foreach ($data as $key => $value) { 
      $name[$key]=$value['id'];
      $data[$key]['role']=Db::table("member_role")->where("mid",$name[$key])->select();
      foreach ($data[$key]['role'] as $key1 => $value1) {
        $data[$key]['role'][$key1]['rname']=Db::table("role")->where("id",$value1['rid'])->value('rname');
        $roles[] = $data[$key]['role'][$key1]['rname'];
        var_dump($roles);
      }
      $data[$key]['roles']=$data[$key]['role'][$key1]['rname'];
    }
    // echo "<pre>";
    // print_r($data);exit;
    $sexarr=['0'=>"保密",'1'=>"男",'2'=>"女"];
    $statusarr=['0'=>"禁用用户",'1'=>"正常用户",'2'=>"管理员用户"];
    $rankarr=['1'=>"青铜会员",'2'=>"白银会员",'3'=>"黄金会员",'4'=>"铂金会员",'5'=>"钻石会员",'6'=>"大师会员",'7'=>"王者会员"];

    return $this->fetch("Admin/admin_list",['sexarr'=>$sexarr,'rankarr'=>$rankarr,'statusarr'=>$statusarr,'data'=>$data]);
  }
  //加载会员编辑修改页面
  public function getAdmin_edit($id){
    $info=Db::table("member")->where("id",$id)->find();
    return $this->fetch("/Admin/admin_edit",['info'=>$info]);
  }
  //执行会员编辑修改
  public function postDoadmin_edit(){
    $request=request();
    $id=$request->param("id");
    $info=$request->except(['id','action']);
    $row=Db::table("member")->where("id",$id)->update($info);
    if ($row) {
      $this->success("信息修改成功","/adminadmin/admin_list");
    }else{
      $this->error("信息修改失败","/adminadmin/admin_edit");
    }
  }

  // ---------------分配角色---------------------//
  

  //加载分配角色模板
  public function getRolelist(){
    $request=request();
    //获取该用户信息
    $userinfo=Db::table("member")->where("id",$request->param("id"))->find();
    //获取所有的角色信息
    $allrole=Db::table("role")->select();
    //当前用于的所有角色信息
    $userrole=Db::table("member_role")->where("mid",$request->param('id'))->select();
    $roleids=[]; 
    foreach($userrole as $val){
      $roleids[]=$val['rid'];
    }
    return $this->fetch("Admin/admin_rolelist",['userinfo'=>$userinfo,'allrole'=>$allrole,'roleids'=>$roleids]);
  }

  //执行角色分配的保存
  public function postSaverole(){
    $request=request();
    Db::table("member_role")->where("mid",$request->param("id"))->delete();
    $newrole=$_POST['roleids']; 
    foreach($newrole as $value){
        $data['mid']=$request->param("id");
        $data['rid']=$value;
        Db::table("member_role")->insert($data);
    }
    $this->success("角色分配成功","/adminadmin/admin_list");
  }

  



}

?>
