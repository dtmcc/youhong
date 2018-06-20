<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use think\Session;

//加载第三方短信类 
function phone($s){
	//加载过来 初始化必填
	Vendor("lib.Ucpaas");
	//请求云之讯平台
	$options['accountsid']='faa57cb6deee97f37ad3977d7274b4bd';
	$options['token']='6767a387bbd30a1ea3fe811260cfa114';
	//初始化 $options必填
	$ucpass = new Ucpaas($options);
	//开发者账号信息查询默认为json或xml
	// header("Content-Type:text/html;charset=utf-8");
	//短信接口调用
	// //短信验证码（模板短信）,默认以65个汉字（同65个英文）为一条（可容纳字数受您应用名称占用字符影响），超过长度短信平台将会自动分割为多条发送。分割后的多条短信将按照具体占用条数计费。
	$appId = "673407ca41014314a27b5e8592c35151";
	//终端对象
	$to = $s;
	//短信模板id
	$templateId = "241792";
	//验证码
	$code=rand(10000,99999);
	Session::set('code',$code);
	//返回的结果
	//转换为php
	$a=json_decode($ucpass->templateSMS($appId,$to,$templateId,$code),true);
	$data[]=$a;
	//转换为json格式
	echo json_encode($data);
}
// **************************加载邮件发送******************************
//  $to接收方  $title主题  $content内容 
function sendmail($to,$title,$content){
	// 加载PHPMailer
	$mail=new \Org\Util\PHPMailer();
	//加载phpMailer类
	$mail=new \Org\Util\PHPMailer();
	//设置字符集
	$mail->CharSet="utf-8";
	//设置采用SMTP方式发送邮件
	$mail->IsSMTP();
	//设置邮件服务器地址
	$mail->Host="smtp.163.com";//C 获取配置文件信息 
	//设置邮件服务器的端口 默认的是25  如果需要谷歌邮箱的话 443 端口号
	$mail->Port=25;
	//设置发件人的邮箱地址
	$mail->From="my_smallpig@163.com"; //
	// $mail->FromName='我';//
	//设置SMTP是否需要密码验证
	$mail->SMTPAuth=true;
	//发送方
	$mail->Username="my_smallpig@163.com";
	$mail->Password="lh19940914";//C客户端的授权密码
	//发送邮件的主题
	$mail->Subject=$title;
	//内容类型 文本型
	$mail->AltBody="text/html";
	//发送的内容
	$mail->Body=$content;
	//设置内容是否为html格式
	$mail->IsHTML(true);
	//设置接收方
	$mail->AddAddress(trim($to));
	if(!$mail->Send()){
		return false;
		// echo "失败".$mail->ErrorInfo;
	}else{
		return true;
	}
}