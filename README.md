PHP library for Jpegify.com API
==========

With this official Jpegify PHP library you can plug into the power and speed of [Jpegify.com](https://jpegify.com/) Image Optimizer.

* [Installation](#installation)
* [Getting Started](#getting-started)
* [Downloading Images](#downloading-images)
* [How To Use](#how-to-use)
* [Wait and Callback URL](#wait-and-callback-url)
  * [Wait Option](#wait-option)
  * [Callback URL](#callback-url)
* [Authentication](#authentication)
* [Usage - Image URL](#usage---image-url)
* [Usage - Image Upload](#usage---image-upload)
* [Usage - Image Buffer](#usage---image-buffer)
* [Lossy Optimization](#lossy-optimization)
* [Image Resizing](#image-resizing)


## Installation

### Git

If you already have git, the easiest way to download the Jpegify-PHP library is with the git command:

```
git clone git://github.com/jpegify/jpegify-php-client.git /path/to/include/jpegify
```

### By Hand

Alternatively, you may download the PHP files from GitHub and place them within your PHP project:

```
https://github.com/jpegify/jpegify-php-client/archive/master.zip
```


## Getting Started

First you need to sign up for the [Jpegify API](https://jpegify.com/plans-billing) and obtain your unique **API Key** and **API Secret**. You will find both under [API Credentials](https://jpegify.com/api-settings). Once you have set up your account, you can start using Jpegify API in your applications.

## Downloading Images

Remember - never link to optimized images offered to download. You have to download them first, and then replace them in your websites or applications. Due to security reasons optimized images are available on our servers **for one hour** only.

## How to use

You can optimize your images in three ways - by providing an URL of the image you want to optimize, by uploading binary image data and by uploading an image file directly to Jpegify API.

The first option (image URL) is great for images that are already in production or any other place on the Internet. The second one (binary image uploading) is ideal if you have binary image data during code execution. The third one (direct upload) is ideal for your deployment process, build script or the on-the-fly processing of your user's uploads where you don't have the images available online yet.

## Wait and Callback URLe

Jpegify gives you two options for fetching optimization results. With the `wait` option set the results will be returned immediately in the response. With the `callback_url` option set the results will be posted to the URL specified in your request. Callback option will be activated in the future, currently not supported.

### Wait option

With the `wait` option turned on for every request to the API, the connection will be held open until the image has been optimized. Once this is done you will get an immediate response with a JSON object containing your optimization results. To use this option simply set `"wait": true` in your request.

**Request:**

````js
{
    "auth": {
        "api_key": "your-api-key",
        "api_secret": "your-api-secret"
    },
    "url": "http://example.com/image.jpg",
    "wait": true
}
````

**Response**

````js
{
    "success": true,
    "file_name": "file.jpg",
    "original_size": 518997,
    "new_size": 24423,
    "original_width": 1200,
    "original_height": 1800,
    "download_url": "https://c.jpegify.com/api/w/8/9f/9be/c444/9a/44/e/file.jpg"
}
````

### Callback URL

Currently not supported. With the Callback URL the HTTPS connection will be terminated immediately and a unique `id` will be returned in the response body. After the optimization is over Jpegify will POST a message to the `callback_url` specified in your request. The ID in the response will reflect the ID in the results posted to your Callback URL.

We will support callback url in the future.

## Authentication

The first step is to authenticate to Jpegify API by providing your unique API Key and API Secret while creating a new Jpegify instance:

````php
<?php

require_once("Jpegify.php");

$jpegify = new Jpegify("your-api-key", "your-api-secret");
````

## Usage - Image URL

To optimize an image by providing image URL use the `$jpegify->fromUrl()` method. `$jpegify->fromUrl()->toFile()` method chaining allows you providing source and target in one line. After `$jpegify->fromUrl()` executed, you can call `$jpegify->toFile()` many times. Image will be saved from cache.


````php
<?php

require_once("Jpegify.com");

$jpegify = new Jpegify("your-api-key", "your-api-secret");

//Example #1: basic optimization, no resizing
$result = $jpegify->fromUrl($url)->toFile("optimized.jpg");

//Example #2: Resizing: width:300  height:auto  strategy:scale  
$result = $jpegify->fromUrl($url, null, 100, null, 'scale')->toFile("optimized.jpg");

//Example #3: Resizing: width:auto height:200  strategy:scale   
$result = $jpegify->fromUrl($url, null, null, 200, 'scale')->toFile("optimized.jpg");

//Example #4: Resizing: width:400 height:400  strategy:fit  default filling color:#FFFFFF  
$result = $jpegify->fromUrl($url, null, 400, 400, 'fit')->toFile("optimized.jpg");

//Example #5: Resizing: width:400 height:400  strategy:fit  filling color:#0de01b
$result = $jpegify->fromUrl($url, null, 400, 400, 'fit:#0de01b')->toFile("optimized.jpg");

//Example #6: Resizing: width:640 height:480  strategy:cover
$result = $jpegify->fromUrl($url, null, 640, 480, 'cover')->toFile("optimized.jpg");

````

Depending on a chosen response option (Wait or Callback URL) in the `data` array you will find either the optimization ID or optimization results containing a `success` property, file name, original file size, optimized file size, amount of savings and optimized image URL:

````php
array(9) {
  ["success"]=>
  bool(true)
  ["file_name"]=>
  string(16) "bcd6dca16ec7.jpg"
  ["original_size"]=>
  int(852941)
  ["new_size"]=>
  int(177685)
  ["original_width"]=>
  int(1200)
  ["original_height"]=>
  int(1800)
  ["download_url"]=>
  string(73) "https://c.jpegify.com/api/w/8/9f/9beff/7839c445/dc/91/4a/bcd6dca16ec7.jpg"
  ["saved_bytes"]=>
  int(675256)
  ["saved_percent"]=>
  string(5) "79.17"
}
````

## Usage - Image Upload

If you want to upload your images directly to Jpegify API use the `$jpegify->fromFile()->toFile()` method. `$jpegify->fromUrl()->toFile()` method chaining allows you providing source and target in one line. After `$jpegify->fromUrl()` executed, you can call `$jpegify->toFile()` many times. Image will be saved from cache.

````php
<?php

require_once("Jpegify.com");

$jpegify = new Jpegify("your-api-key", "your-api-secret");

$file = "set-your-file-here";

//Example #1: basic optimization, no resizing
$result = $jpegify->fromFile($file)->toFile("optimized.jpg");

//Example #2: Resizing: width:300  height:auto  strategy:scale  
$result = $jpegify->fromFile($file, null, 100, null, 'scale')->toFile("optimized.jpg");

//Example #3: Resizing: width:auto height:200  strategy:scale   
$result = $jpegify->fromFile($file, null, null, 200, 'scale')->toFile("optimized.jpg");

//Example #4: Resizing: width:400 height:400  strategy:fit  default filling color:#FFFFFF  
$result = $jpegify->fromFile($file, null, 400, 400, 'fit')->toFile("optimized.jpg");

//Example #5: Resizing: width:400 height:400  strategy:fit  filling color:#0de01b
$result = $jpegify->fromFile($file, null, 400, 400, 'fit:#0de01b')->toFile("optimized.jpg");

//Example #6: Resizing: width:640 height:480  strategy:cover
$result = $jpegify->fromFile($file, null, 640, 480, 'cover')->toFile("optimized.jpg");

````

## Usage - Image Buffer

If you want to upload your binary image data directly to Jpegify API use the `$jpegify->fromBuffer()` method. `$jpegify->fromUrl()->toFile()` method chaining allows you providing source and target in one line. After `$jpegify->fromUrl()` executed, you can call `$jpegify->toFile()` many times. Image will be saved from cache.

````php
<?php

require_once("Jpegify.com");

$jpegify = new Jpegify("your-api-key", "your-api-secret");

//set your file path
$file = "set-your-file-here";
$binaryImageData = file_get_contents($file);

//Example #1: basic optimization, no resizing
$result = $jpegify->fromBuffer($binaryImageData)->toFile("optimized.jpg");

//Example #2: Resizing: width:300  height:auto  strategy:scale  
$result = $jpegify->fromBuffer($binaryImageData, null, 100, null, 'scale')->toFile("optimized.jpg");

//Example #3: Resizing: width:auto height:200  strategy:scale   
$result = $jpegify->fromBuffer($binaryImageData, null, null, 200, 'scale')->toFile("optimized.jpg");

//Example #4: Resizing: width:400 height:400  strategy:fit  default filling color:#FFFFFF  
$result = $jpegify->fromBuffer($binaryImageData, null, 400, 400, 'fit')->toFile("optimized.jpg");

//Example #5: Resizing: width:400 height:400  strategy:fit  filling color:#0de01b
$result = $jpegify->fromBuffer($binaryImageData, null, 400, 400, 'fit:#0de01b')->toFile("optimized.jpg");

//Example #6: Resizing: width:640 height:480  strategy:cover
$result = $jpegify->fromBuffer($binaryImageData, null, 640, 480, 'cover')->toFile("optimized.jpg");

````

## Lossy Optimization

When you decide to sacrifice just a small amount of image quality (usually unnoticeable to the human eye), you will be able to save up to 90% of the initial file weight. Lossy optimization will give you outstanding results with just a fraction of image quality loss.

Lossy optimization is default behaviour, no need to do anything.


## Image Resizing

Image resizing option is great for creating thumbnails or preview images in your applications. Jpegify will first resize the given image and then optimize it with its vast array of optimization algorithms. The `resize` option needs a few parameters to be passed like desired `width` and/or `height` and a mandatory `strategy` property. For example:

````php
<?php

require_once("Jpegify.com");

$jpegify = new Jpegify("your-api-key", "your-api-secret");

//Example #1: Resizing: width:300  height:auto  strategy:scale  
$result = $jpegify->fromUrl($url, null, 100, null, 'scale')->toFile("optimized.jpg");

//Example #2: Resizing: width:auto height:200  strategy:scale   
$result = $jpegify->fromUrl($url, null, null, 200, 'scale')->toFile("optimized.jpg");

//Example #3: Resizing: width:400 height:400  strategy:fit  default filling color:#FFFFFF  
$result = $jpegify->fromUrl($url, null, 400, 400, 'fit')->toFile("optimized.jpg");

//Example #4: Resizing: width:400 height:400  strategy:fit  filling color:#0de01b
$result = $jpegify->fromUrl($url, null, 400, 400, 'fit:#0de01b')->toFile("optimized.jpg");

//Example #5: Resizing: width:640 height:480  strategy:cover
$result = $jpegify->fromUrl($url, null, 640, 480, 'cover')->toFile("optimized.jpg");

````

The `strategy` property can have one of the following values:

- `scale` - Scales the image down proportionally. You must provide either a target width or a target height, but not both. The scaled image will have exactly the provided width or height.
- `fit` - Scales the image down proportionally so that it fits within the given dimensions. You must provide both a width and a height. The scaled image will not exceed either of these dimensions.
- `cover` - Scales the image proportionally and crops it if necessary so that the result has exactly the given dimensions. You must provide both a width and a height. Which parts of the image are cropped away is determined automatically. An intelligent algorithm determines the most important areas and leaves these intact.

**More information about image resizing and cropping can be found in the [Jpegify API Reference](https://jpegify.com/docs/image-resizing)**

## LICENSE - MIT

Copyright (c) 2017 Jpegify.com

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.