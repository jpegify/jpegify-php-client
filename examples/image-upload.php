<?php
 /**
  * Image uploading and optimazing with Jpegify PHP Client
  * 
  */
require_once("../lib/Jpegify.php");

//put your api key and secret. Visit your api key and secret: https://jpegify.com/api-settings
$jpegify = new Jpegify("your-api-key", "your-api-scret");

//run for enabling sandbox. when you developing, enable sandbox to prevent your plan.
//$jpegify->enableSandbox();

//set your file path
$file = "set-your-file-here";
$file = "/Users/orhan/Desktop/images/trendyol/3010000958079_1_org_zoom.jpg";

//Example #1: basic optimization, no resizing
$result = $jpegify->fromFile($file)->toFile("optimized.jpg");

//Example #2: Resizing: width:300  height:auto  strategy:scale  
//$result = $jpegify->fromFile($file, null, 100, null, 'scale')->toFile("optimized.jpg");

//Example #3: Resizing: width:auto height:200  strategy:scale   
//$result = $jpegify->fromFile($file, null, null, 200, 'scale')->toFile("optimized.jpg");

//Example #4: Resizing: width:400 height:400  strategy:fit  default filling color:#FFFFFF  
//$result = $jpegify->fromFile($file, null, 400, 400, 'fit')->toFile("optimized.jpg");

//Example #5: Resizing: width:400 height:400  strategy:fit  filling color:#0de01b
//$result = $jpegify->fromFile($file, null, 400, 400, 'fit:#0de01b')->toFile("optimized.jpg");

//Example #6: Resizing: width:640 height:480  strategy:cover
//$result = $jpegify->fromFile($file, null, 640, 480, 'cover')->toFile("optimized.jpg");

echo("\n");
if (!empty($result["success"])) {

	// optimization succeeded
	echo "Success. Optimized image URL: " . $result["download_url"]."\n";
} elseif (isset($result["error"])) {

	// something went wrong with the optimization
	echo "Optimization failed. Error message from jpegify.com: " . $result["error"]."\n";
} else {

	// something went wrong with the request
	echo "cURL request failed. Error message: " . $result["error"]."\n";
}

print_r($result);
echo("\n");


