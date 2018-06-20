<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
//导入Config.php
use think\Config;
class Index extends Allow{
  public function getIndex(){
    return $this->fetch("Index/index");
  }
}

?>
