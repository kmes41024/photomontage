<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/include/function_debug.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/utilinc/function_utility.php');
session_start();

if (isset($_POST['base64']))
	$imgBase64 = $_POST['base64'];
else
{
	echo '{"state":"fail","msg":"没有base64参数"}';
	exit;
}

$url = processImg($imgBase64, "./");

if ($url != null)
{
	// 创建背景图片资源
    $bg = 'img/bg.png';  //背景图片(合成的图片大小是背景图片的大小)
    $dest_img = Imagecreatefrompng($bg);  // 创建背景图片资源
	 // 进行图片的合成
    $source= Imagecreatefrompng($url); 
	// 缩略图比例
	$percent = 0.5;

	// 缩略图尺寸
	list($width, $height) = getimagesize($url);
	$newwidth = $width * $percent;
	$newheight = $height * $percent;
	imagecopyresized($dest_img, $source, 100, 10, 0, 0, 400, 400, $width, $height);
	$dirName = "shared";
	if (!file_exists($dirName)) 
	{
	  //检查是否有该文件夹，如果没有就创建，并给予最高权限
	  @mkdir($dirName, 0700);
	}	
	$regkey = FRndKey(8);
	$fileName = "shared/{$regkey}.jpg";
	$export = "http://192.168.1.156:81/picture/".$fileName;
	Imagejpeg($dest_img, $fileName);
	@unlink($url);
	echo '{"state":"success","msg":"成功", "url":"'.$fileName.'", "export":"'.$export.'"}';
}
else
	echo '{"state":"fail","msg":"存档失败", "url":"null"}';
exit;

function processImg($base64_image_content, $dirName)
{
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/',$base64_image_content,$result))
	{
		$type = $result[2];//图片后缀
		//$new_file = $_SERVER['DOCUMENT_ROOT'].'/upload/';
		if (!file_exists($dirName)) 
		{
		  //检查是否有该文件夹，如果没有就创建，并给予最高权限
		  mkdir($dirName, 0700);
		}

		$filename = time() . '_' . uniqid() . ".{$type}"; //文件名
		$new_file = $dirName . $filename;
		
		//写入操作
		if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content))))
		{
			return $new_file;
		}else
		{
			echo null;
		}
	}
	return null;
}
?>