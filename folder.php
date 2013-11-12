<?php
/**
 * Created by JetBrains PhpStorm.
 * User: geecue22
 * Date: 2013-10-08
 * Time: 11:02 AM
 * To change this template use File | Settings | File Templates.
 */

error_reporting(E_ALL ^ E_NOTICE);

require_once 'includes/rest_connector2.php';
require_once 'includes/session.php';

checksession();

$rest = new RESTConnector();

$urlbase = 'https://'.$_POST['server'].':'.$_POST['port'].'/api/';


$url = $urlbase.'users/?filter=';
$filter = '(username CONTAINS[cd] "'.$_POST['user'].'")';
$rest->createRequest($url.rawurlencode($filter),"GET",null,$_SESSION['cookies'][0],$_POST['user'],$_POST['pass']);
$rest->sendRequest();
$response = $rest->getResponse();
$error = $rest->getError();
$exception = $rest->getException();

// save our session cookies
if ($_SESSION['cookies']==null)
    $_SESSION['cookies'] = $rest->getCookies();

// display any error message
if ($error!=null)
    die('LOGIN ERROR: '.$error);

// display any error message
if ($error!=null)
    die('LOGIN EXCEPTION: '.$exception);

if ($response!=null || $response!="") {
    $temp = simplexml_load_string($response);

    writeconfig($_POST['server'],$_POST['port'],$_POST['user'],$_POST['pass'],$temp->user->name->first,$temp->user->name->last);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>LSS Image Extractor</title>
</head>
<body>
<h1>LSS Image Extractor</h1>
<p>Logged in as: <b><?php echo $temp->user->name->first.' '.$temp->user->name->last; ?> </b></p>
<p>
<form action="image.php" method="GET">
    <table>
        <tr>
            <td>
                Please define a new folder. All images and the spreadsheet will be saved to this folder.
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" maxlength="20" size="20" name="folder">
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <input type="submit" value="BEGIN" name="begin">
            </td>
        </tr>
    </table>
</form>
</p>

<?php
}
else
    echo "There was no response. Please go back and try again.";
//

?>
</body>
</html>