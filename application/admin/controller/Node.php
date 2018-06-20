<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
//导入Config.php
use think\Config;
class Node  extends Controller{
  //加载权限列表页
  public function getNode_list(){
    $data=Db::table("node")->select();
    return $this->fetch("Node/node_list",['data'=>$data]);
  }
  //加载权限添加页面
  public function getNode_add(){
    return $this->fetch("Node/node_add");
  }
  //执行权限的添加
  public function postDonode_add(){
    $request=request();
    $data=$request->except(['action']);
    $row=Db::table("node")->insert($data);
    if ($row) {
      $this->success("权限添加成功","/adminnode/node_list");
    }else{
      $this->error("权限添加失败","/adminnode/node_list");
    }
  }

}

?>
