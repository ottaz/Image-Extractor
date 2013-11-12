<?php
/*
 * Updated on May 10, 2013
 *
 * image.php
 * 
 * Extracts the images from all current products, saves them into a folder
 * and creates a tab delimited .txt file for re-import purposes
 */

error_reporting(E_ALL ^ E_NOTICE);
ini_set('max_execution_time', 300);

require_once 'includes/rest_connector2.php';
require_once 'includes/session.php';

checksession();

//print_r($_SESSION);

if (isset($_GET['begin']) && $_GET['folder']!="")
{
    $config = array();

    if (file_exists("config/config.php"))
    {
        $tmpconfig = require("config/config.php");
        if (isset($tmpconfig['urlbase']))
            $config['urlbase'] = $tmpconfig['urlbase'];

        if (isset($tmpconfig['user']))
            $config['user'] = $tmpconfig['user'];

        if (isset($tmpconfig['pass']))
            $config['pass'] = $tmpconfig['pass'];

        if (isset($tmpconfig['first']))
            $config['first'] = $tmpconfig['first'];

        if (isset($tmpconfig['last']))
            $config['last'] = $tmpconfig['last'];
    }
    else
        die("ERROR: Missing configuration parameters.");

    ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>LSS Image Extractor</title>
</head>
<body>
<h1>LSS Image Extractor</h1>
<!--<p>Logged in as: <b><?php //echo $_SESSION['first'].' '.$_SESSION['last']; ?> </b></p> -->

    <!-- Progress bar product count -->
    <div id="productcountprogress" style="width:500px;border:1px solid #ccc;"></div>
    <!-- Progress bar product count -->
    <div id="productcountinfo" ></div>


    <!-- Progress bar Classes -->
    <br />
    <div id="imagecountprogress" style="width:500px;border:1px solid #ccc;"></div>
    <!-- Progress bar image count -->
    <div id="imagecountinfo" ></div>

    <br />
    <div id="imagecreateprogress" style="width:500px;border:1px solid #ccc;"></div>
    <!-- Progress bar image save -->
    <div id="imagecreateinfo" ></div>

    <br />
    <div id="ssprogress" style="width:500px;border:1px solid #ccc;"></div>
    <!-- Progress bar spreadsheet -->
    <div id="ssinfo" ></div>

    <?php

    $rest = new RESTConnector();

    $headers = true;
    $count=0;

    $url = $config['urlbase'].'products/';
    $productarray = array();
    $imagearray = array();

    $rest->createRequest($url,"GET", null, $_SESSION['cookies'][0], $config['user'], $config['pass']);

    echo '<script language="javascript">
    document.getElementById("productcountinfo").innerHTML="Standby...: '.null.'";
    </script>';
    flush();

    $rest->sendRequest();
    $response = $rest->getResponse();
    $error = $rest->getError();
    $exception = $rest->getException();


    // save our session cookies
    if ($_SESSION['cookies']==null)
        $_SESSION['cookies'] = $rest->getCookies();

    // display any error message
    if ($error!=null)
        die('GET PRODUCTS ERROR: '.$error);

    if ($exception!=null)
        die('GET PRODUCTS EXCEPTION: '.$exception);

    // display the response
    if ($response!=null || $response=="")
    {

        $temp = simplexml_load_string($response);
        $total = count($temp);
        $x=0;
        while ($temp->product[$x])
        {
            $productarray[$x] = array('code'=>$temp->product[$x]->code,
                                      'id'=>$temp->product[$x]->attributes()->id);
            $x++;

            $percent = intval($x/$total * 100)."%";

            // Javascript for updating the progress bar and information
            echo '<script language="javascript">
    document.getElementById("productcountprogress").innerHTML="<div style=\"width:'.$percent.';background-color:#ddd;\">&nbsp;</div>";
    document.getElementById("productcountinfo").innerHTML="Loading Products: '.$x.' of '.$total.'";
    </script>';

            // This is for the buffer achieve the minimum size in order to flush data
            echo str_repeat(' ',1024*64);

            // Send output to browser immediately
            flush();
        }
    }
    else
        echo "There was no response.";

    $x=0; $y=0; $noimage=0;

    while ($productarray[$x])
    {

        $url1 = $config['urlbase'].'products/'.$productarray[$x]['id'].'/product_photos/';

        $rest->createRequest($url1,"GET", null, $_SESSION['cookies'][0], $config['user'], $config['pass']);
        $rest->sendRequest();
        $response = $rest->getResponse();
        $error = $rest->getError();
        $exception = $rest->getException();

        // save our session cookies
        if ($_SESSION['cookies']==null)
	        $_SESSION['cookies'] = $rest->getCookies();

        // display any error message
        if ($error!=null)
	        die('GET PRODUCTS w PHOTOS ERROR: '.$error);

        if ($exception!=null)
	        die('GET PRODUCTS w PHOTOS ERROR: '.$exception);

        if ($response!=null || $response=="")
        {

            $temp1 = simplexml_load_string($response);

            if ($temp1)
            {
                //print_r($temp1);
                $imagearray[$y] = $productarray[$x];
                $imagearray[$y]['imageid'] = $temp1->product_photo->attributes()->id;
                $y++;
                //echo 'Image id: ' . $imagearray[$x]['imageid'] .'<br>';
            }
            else
                $noimage++;

        }
        else
            echo "There was no response.";

        $x++;

        $percent = intval($x/$total * 100)."%";

        // Javascript for updating the progress bar and information
        echo '<script language="javascript">
    document.getElementById("imagecountprogress").innerHTML="<div style=\"width:'.$percent.';background-color:#ddd;\">&nbsp;</div>";
    document.getElementById("imagecountinfo").innerHTML="Isolating Products w photos: '.$x.'";
    </script>';

        // This is for the buffer achieve the minimum size in order to flush data
        echo str_repeat(' ',1024*64);

        // Send output to browser immediately
        flush();

    }

    // Javascript for updating the progress bar and information
    echo '<script language="javascript">
    document.getElementById("imagecountprogress").innerHTML="<div style=\"width:100%;background-color:#ddd;\">&nbsp;</div>";
    document.getElementById("imagecountinfo").innerHTML="Isolating Products w photos: '.$x.'";
    </script>';

    // This is for the buffer achieve the minimum size in order to flush data
    echo str_repeat(' ',1024*64);

    // Send output to browser immediately
    flush();

    //echo 'With photos: ' . $y . ', ';
    //echo 'No photos: ' .$noimage . ', ';
    //sleep(1);

    //sleep(10);

    $x=0; $imageadded=0; $imagenotadded=0;

    $total = count($imagearray);

    foreach ($imagearray as $k => $v)
    {

        $url1 = 'https://localhost:9630/api/products/'.$v['id'].'/product_photos/'.$v['imageid'].'/image/';
        $rest->createRequest($url1,"GET", null, $_SESSION['cookies'][0], $config['user'], $config['pass']);
        $rest->addHeader("Accept","image/*; size=original");

        $rest->sendRequest();
        $response = $rest->getResponse();
        $error = $rest->getError();
        $exception = $rest->getException();

        // save our session cookies
        if ($_SESSION['cookies']==null)
            $_SESSION['cookies'] = $rest->getCookies();

        // display any error message
        if ($error!=null)
            die('GET IMAGES: '.$error);

        if ($error!=null)
            die('GET IMAGES: '.$error);

        if ($response!=null || $response=="")
        {
            $im = imagecreatefromstring($response);

            if (is_writable($_GET['folder']))
            {
                // echo 'writeable';
                //echo '<br><br>';
                header("Content-type: image/jpeg");
                if (imagejpeg($im, $_GET['folder'].'/'.$v['id'].'.jpg', 100))
                    //echo 'Image creation successful: ' . $imagearray[$x]['code'];
                $imageadded++;
                else
                    $imagenotadded++;
                imagedestroy($im);
            }
        }

        $percent = intval($imageadded/$total * 100)."%";

        // Javascript for updating the progress bar and information
        echo '<script language="javascript">
        document.getElementById("imagecreateprogress").innerHTML="<div style=\"width:'.$percent.';background-color:#ddd;\">&nbsp;</div>";
        document.getElementById("imagecreateinfo").innerHTML="Images added to folder: '.$imageadded.' of '.$total.'";
        </script>';

        // This is for the buffer achieve the minimum size in order to flush data
        echo str_repeat(' ',1024*64);

        // Send output to browser immediately
        flush();
    }


    //sleep(1);
//    echo 'Images added: '.$imageadded.', ';
//    echo 'Images not added: '.$imagenotadded.', ';
    @mkdir($folder,0777,true);
    $textfile = $folder.'/updateimages.txt';
    $tab = "\t"; $newline = "\r";

    if (!$handle = fopen($textfile, 'a'))
        die('Cannot open file '.$textfile);

//    if ($headers === false)
        if (fwrite($handle, 'Product Code'.utf8_encode($tab).
                            'Photo'.utf8_encode($newline)) === false)
            die('Cannot write to file '.$textfile);
        else
            $headers = true;


    $total = count($imagearray);

    foreach ($imagearray as $k => $v)
    {
        if (fwrite($handle, $v['code'].utf8_encode($tab).
                $v['id'].'.jpg'.utf8_encode($newline)) === false)
            die('Cannot write to file '.$textfile);

        $percent = intval(($k+1)/$total * 100)."%";

        // Javascript for updating the progress bar and information
        echo '<script language="javascript">
        document.getElementById("ssprogress").innerHTML="<div style=\"width:'.$percent.';background-color:#ddd;\">&nbsp;</div>";
        document.getElementById("ssinfo").innerHTML="Rows added to spreadsheet file: '.($k+1).' of '.$total.'";
        </script>';

        // This is for the buffer achieve the minimum size in order to flush data
        echo str_repeat(' ',1024*64);

        // Send output to browser immediately
        flush();

    }

//    echo $x . ' rows added to file<br>';

    fclose($handle);

//$count++;
//}
//
//$timeended = time();
////echo 'End time: '. $timeended = time();
//
//echo 'Elapsed time: ';
//echo $timeended-$timestarted;
//echo ' seconds';
//
}

    else
        echo 'You did not define a folder name. Please go back and do so.';

?>

</body>
</html>