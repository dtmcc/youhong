<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------


//导入路由类
use think\Route;

//-----------------网站后台路由-----------//

//后台登录，退出路由 
Route::controller("/adminlogin","admin/Login");
//后台首页路由 
Route::controller("/adminindex","admin/Index");
//后台用户路由
Route::controller("/adminmember","admin/Member");
//后台管理员路由
Route::controller("/adminadmin","admin/Admin");
//后台角色路由
Route::controller("/adminrole","admin/Role");
//后台节点(权限)路由
Route::controller("/adminnode","admin/Node");
//后台类别路由
Route::controller("/admincategory","admin/Category");
//后台商品路由
Route::controller("/adminproduct","admin/Product");
//后台订单路由
Route::controller("/adminorder","admin/Order");
//后台评价路由
Route::controller("/admincomment","admin/Comment");
//后台公告路由
Route::controller("/adminnotice","admin/Notice");
//后台广告路由
Route::controller("/adminadver","admin/Adver");
//后台广告数据路由
Route::controller("/adminadverpro","admin/Adverpro");
//后台友情链接路由
Route::controller("/adminfriendlink","admin/Friendlink");
//后台留言路由
Route::controller("/adminmessage","admin/Message");
//后台Ajax专用路由
Route::controller("/adminajax","admin/Ajax");



//-----------------网站前台路由-----------//
//前台Ajax专用路由
Route::controller("/homeajax","home/Ajax");
//前台主页路由
Route::controller("/homeindex","home/Index");
//前台注册路由
Route::controller("/homeregister","home/Register");
//前台登录，退出路由
Route::controller("/homelogin","home/Login");
//前台商品列表路由
Route::controller("/homeproductlist","home/Productlist");
//前台商品详情路由
Route::controller("/homeproductinfo","home/Productinfo");
//前台个人中心路由
Route::controller("/homepersonal","home/Personal");
//前台购物车路由
Route::controller("/homecar","home/Car");
