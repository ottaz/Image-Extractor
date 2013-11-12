<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>LSS API Login</title>
</head>
<body>
<h1 style="padding-left: 10px;">Login</h1>

<?php
/**
 * Created by JetBrains PhpStorm.
 * User: geecue22
 * Date: 2013-10-07
 * Time: 7:43 PM
 * To change this template use File | Settings | File Templates.
 */
?>
<form method="post" action="folder.php">
    <table style="padding-left: 10px;">
        <tr>
            <td>Server</td>
            <td>
                <input type="text" maxlength="15" size="15" name="server" value="localhost" />
            </td>
        </tr>
        <tr>
            <td>Port</td>
            <td>
                <input type="text" maxlength="4" size="4" name="port" value="9630" />
            </td>
        </tr>
        <tr>
            <td>Username</td>
            <td>
                <input type="text" maxlength="25" size="25" name="user" />
            </td>
        </tr>
        <tr>
            <td>Password</td>
            <td>
                <input type="password" maxlength="25" size="25" name="pass" />
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>
                <input type="submit" value="LOGIN">
            </td>
        </tr>
    </table>
</form>
</body>
</html>