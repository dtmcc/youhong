<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
//导入Config.php
use think\Config;
class Role extends Controller{
  //加载角色列表模板
  public function getRole_list(){
    $data=Db::table("role")->select();
    return $this->fetch("Role/role_list",['data'=>$data]);
  }

  //加载添加角色模板
  public function getRole_add(){
    return $this->fetch("Role/role_add");
  }

  //执行角色添加
  public function postDorole_add(){
    $request=request();
    $data=$request->except(['action']);
    $row=Db::table("role")->insert($data);
    if ($row) {
      $this->success("角色添加成功","/adminrole/role_list");
    }else{
      $this->error("角色添加失败，请重新添加","/adminrole/role_list");
    }
  }

  //加载角色的编辑页面
  public function getRole_edit(){
    $request=request();
    $info=Db::table("role")->where("id",$request->param('id'))->find();
    return $this->fetch("Role/role_edit",['info'=>$info]);
  }
  
  //执行角色的编辑修改
  public function postDorole_edit(){
    $request=request();
    $data=$request->except(['action','id']);
    $row=Db::table("role")->where('id',$request->param('id'))->update($data);
    if ($row) {
      $this->success("角色修改成功","/adminrole/role_list");
    }else{
      $this->error("角色修改失败","/adminrole/role_list");
    }
  }

  //ajax删除角色
  public function postRole_del($id){
    //删除角色表中该角色
    Db::table("role")->where('id',$id)->del();
    //同时删除该角色的权限
    DB::table("role_node")->where("rid",$id)->del();
  }

  //为角色分配权限
  public function getNodelist(){
    $request=request();
    // var_dump($request->param('id'));
    //获取当前角色信息
    $roleinfo=Db::table("role")->where("id",$request->param("id"))->find();
    //获取所有的权限信息
    $allnode=Db::table("node")->select();
    //获取当前角色的所有权限
    $rolenode=Db::table("role_node")->where("rid",$request->param("id"))->select();
    $nodeids=[]; 
    foreach($rolenode as $val){
      $nodeids[]=$val['nid'];
    }
    return $this->fetch("Role/role_nodelist",['roleinfo'=>$roleinfo,'allnode'=>$allnode,'nodeids'=>$nodeids]); 
  }
  //保存角色分配的权限节点
  public function postSavenode(){
    $request=request();
    Db::table("role_node")->where("rid",$request->param("id"))->delete();
    $newnode=$_POST['nodeids']; 
    foreach($newnode as $value){
        $data['rid']=$request->param("id");
        $data['nid']=$value;
        Db::table("role_node")->insert($data);
    }
    $this->success("权限分配成功","/adminrole/role_list");
  }
}

?>
