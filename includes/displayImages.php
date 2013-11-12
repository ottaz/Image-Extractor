<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'rest_connector2.php';
require_once 'session.php';

checksession();

function displayPhoto($url) {
    $rest = new RESTConnector();
    $rest->createRequest((string)$url,"GET", null, $_SESSION['cookies'][0]);
    $rest->addHeader("Accept","image/*; size=original");
    $rest->sendRequest();
    $response = $rest->getResponse();
    $error = $rest->getException();
    $_SESSION['cookies'] = $rest->getCookies();		// save our session cookies
    if ($error!=null) echo $error;			// display any error message

    // display the response
    if ($response!=null || $response=="")
        return base64_encode($response);
    else
        return "No image?";
}

?>
