<?php
	function addCode($im, $length, $font)
	{
		//create 2 codes with defined length
		$possible_characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$text1 = substr(str_shuffle(str_repeat($possible_characters, 5)), 0, $length);
		$text2 = substr(str_shuffle(str_repeat($possible_characters, 5)), 0, $length);
		
		//generate random location/angle/color/size for left code
		$size = rand(15,50);
		$color = imagecolorallocate($im, rand(0,160), rand(0,160), rand(0,160));
		$angle = rand(-45,45);
		$x = rand(20,45);
		$y = rand(130,290);
		//add text to image
		imagettftext($im, $size, $angle, $x, $y, $color, $font, $text1);
		
		//generate random location/angle/color/size for right code
		$size = rand(15,50);
		$color = imagecolorallocate($im, rand(0,160), rand(0,160), rand(0,160));
		$angle = rand(-45,45);
		$x = rand(195,220);
		$y = rand(130,290);
		//add text to image
		imagettftext($im, $size, $angle, $x, $y, $color, $font, $text2);
		
		//return complete code
		return ($text1.$text2);
	}
	
	function addDistractor($im)
	{
		$type = rand(0,2);
		//create circle
		if($type == 0){
			$width = rand(20,360);
			$height = rand(20,360);
			$x = rand(20+($width/2),380-($width/2));
			$y = rand(20+($height/2),380-($height/2));
			$color = imagecolorallocate($im, rand(0,160), rand(0,160), rand(0,160));
			imageellipse($im,$x,$y,$width,$height,$color);
		}
		//create line
		else if ($type == 1){
			$x1 = rand(20,380);
			$y1 = rand(20,380);
			$x2 = rand(20,380);
			$y2 = rand(20,380);
			$color = imagecolorallocate($im, rand(0,160), rand(0,160), rand(0,160));
			imageline($im,$x1,$y1,$x2,$y2,$color);
		}
		//create rectangle
		else if ($type == 2){
			$x1 = rand(20,380);
			$y1 = rand(20,380);
			$x2 = rand(20,380);
			$y2 = rand(20,380);
			$color = imagecolorallocate($im, rand(0,160), rand(0,160), rand(0,160));
			imagerectangle($im,$x1,$y1,$x2,$y2,$color);
		}
	}
		
	function makeSendCaptcha($length, $font)
	{
		header("Content-Type: image/png");
		
		//create image
		$im = imagecreatetruecolor (400,400);
	
		//define colors
		$white = imagecolorallocate($im, 255,255,255);
		$black = imagecolorallocate($im, 0, 0, 0);
		$lightyellow = imagecolorallocate($im, 255,255,0);
		$blue = imagecolorallocate($im, 0, 0, 255);
		
		//create frame
		imagefilledrectangle($im, 0, 0, 400, 400, $white);
		imagefilledrectangle($im, 10, 10, 390, 390, $lightyellow);
		
		//add 3 random distractors
		addDistractor($im);
		addDistractor($im);
		addDistractor($im);
		
		//add random charcter code
		$code = addCode($im, $length, $font);
		
		//add easy to read test code on bottom of image
		imagettftext($im, 13, 0,15, 365, $black, 'font/arial.ttf', "session id: " . session_id());
		imagettftext($im, 15, 0,15, 385, $black, 'font/arial.ttf', "captcha: " . $code);
		
		//send image and destroy server file of image
		imagepng($im);
		imagedestroy($im);
		
		//return string of captcha
		return $code;
	}
	
	session_start();
	
	//create captcha and save code as session variable
	$capText = makeSendCaptcha (2,"font/LaBelleAurore.ttf");
	$_SESSION["captcha"] = $capText;
	
?>