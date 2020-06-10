<?php 
/**
 Project: PHP Minecraft Skin
 Author: FastChen (快辰)
 GitHub: https://github.com/FastChen/PHP-Minecraft-Skin
 URL: https://fastchen.com/works/
 Principle： https://fastchen.com/works/minecraftskin.html

 - HOT TO USE -
 https://yousite/?name=your minecraft id (NOT UUID)
 &avatarsize=256 Change avatar size(default 64x64)
**/

    //先判断是否存在老用户头像数据
    if(file_exists("avatar.png")){
        unlink("avatar.png");
    }

    if(empty($_GET['name'])){
        echo "[!] 我觉得你应该输入一个正版Minecraft ID :(";
    }
    else{
        $mojang_uuid = curl_get_https('https://api.mojang.com/users/profiles/minecraft/'.$_GET['name']);
        $de_uuid = json_decode($mojang_uuid, true);
        if(!is_null($de_uuid['id'])){
            $player_profile= curl_get_https('https://sessionserver.mojang.com/session/minecraft/profile/' . $de_uuid['id']);
            $de_profile = json_decode($player_profile, true);

            $de_textures = json_decode(base64_decode($de_profile['properties'][0]['value']), true);

            //头像大小默认 64px
            if(!is_null($_GET['avatarsize'])){
                $size_avatar = $_GET['avatarsize'];
            }else{
                $size_avatar = 64;
            }
            //裁剪并缩放创建头像
            $copyskin = imagecreatetruecolor($size_avatar, $size_avatar);
            $originalskin = imagecreatefromstring(file_get_contents($de_textures['textures']['SKIN']['url']));
            imagecopyresized($copyskin, $originalskin, 0, 0, 8, 8, $size_avatar, $size_avatar, 8, 8);
            //保存生成后头像
            //输出拷贝后图像
            //header("Content-type: image/jpeg");
            imagepng($copyskin,"avatar.png");
            //销毁
            imagedestroy($copyskin);
            imagedestroy($originalskin);
        }else{
            echo "[!] 无法获取UUID :(";
        }
    }
    

    //Thanks internet
    function curl_get_https($url) 
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);  // 跳过检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);  // 跳过检查
        $tmpInfo = curl_exec($curl); 
        curl_close($curl);
        return $tmpInfo;   //返回json对象
    }
 ?>
<!DOCTYPE html>
<html>
 <head> 
  <title>PHP Minecraft Skin By FastChen</title> 
 </head> 
 <body> 
  <h2>PHP Minecraft Skin By FastChen</h2> 
  <hr>
  <h2>如何使用 / How to use</h2>
  <li>http(s)://yoursite/?=your minecraft id(not uuid)</li>
  <p><b>选项 / Options</b></p>
  <li>&avatarsize=256 设置头像大小 Change avatar size(默认 / default 64x64)</li>
  <p><b>示例 / Example</b></p>
  <li>http://yoursite/?name=jeb_&avatarsize=256</li>
  <hr> 
  <h3>玩家皮肤 / User Skin</h3> 
  <?php 
    if(!empty($de_textures['textures']['SKIN']['url'])){
        echo '<img src='.$de_textures['textures']['SKIN']['url'].'>';
    }else{echo '<p>找不到皮肤 Not Have Skin :(</p>';}
  ?>
  <h3>玩家披风 / User Cape</h3>
  <?php 
    if(!empty($de_textures['textures']['CAPE']['url'])){
        echo '<img src='.$de_textures['textures']['CAPE']['url'].'>';
    }else{echo '<p>没有披风 Not Have Cape :(</p>';}
  ?>
  <h3>玩家头像 / User Avatar</h3> 
  <?php 
    if(file_exists("avatar.png")){
        echo '<img src="./avatar.png">';
    }else{echo '<p>出现了什么奇怪的错误 Something wrong. right? :(</p>';}
  ?>
  <hr>
  <b>错误信息 / Error message</b>
 </body>
</html>
