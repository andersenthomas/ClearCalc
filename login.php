<?php

require('common_functions.php');

session_start();

$password = hash("sha256", "clearance");
$username = 'clearance';

if (isset($_POST['username']) || isset($_POST['username']))
{
$password_user = hash("sha256", $_POST['password']);
$username_user = $_POST['username'];
}

else
{
  $password_user = NULL;
  $username_user = NULL;
}

echo(html_header());

// Checks password and user and sets session variables
if(isset($_POST['submit'])){
     if ($username == $username_user && $password == $password_user) {

        // Set session variable
        $_SESSION['LOGGED_IN'] = True;
        $_SESSION['LAST_ACTIVITY'] = time();

        // Redirect to index page
        header("Location: index.php");
        exit;

     } else {
        //Wrong username and password
        echo('<div class="body_text">Forkert brugernavn og/eller password</div><br>');

    }

}

?>

<div class="datacontainer">
    <form action="" method="post">
    <table>
        <tr>
            <td>Brugernavn:</td>
            <td><input type="text" name="username" size="20" autofocus></td>
        </tr>
        <tr>
            <td>Password:</td>
            <td><input type="password" name="password" size="20"></td>
        </tr>
        <tr>
            <td colspan="2" align="right"><input type="submit" name="submit" value="Send"></td>
        </tr>
    </table>
    </form>
</div>

<?php
echo(html_footer());
?>
