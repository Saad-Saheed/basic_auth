<?php
session_write_close();
session_start();

$user = (isset($_SESSION['current_user'])) ? $_SESSION['current_user'] : null;
$user = (object) $user;

if (isset($_GET['action']) && $_GET['action'] == "logout")
    logout();

function logout()
{
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), "", time() - 3600, "/");
    }

    $_SESSION  = [];
    session_destroy();

    header("location: login.php");
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Basic Authetication</title>
</head>

<body>
    <header>
        <nav>
            <ul>
                <?php
                if (!isset($_SESSION['current_user'])) { ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php
                } else {  ?>
                    <li><a href="?action=logout">Logout</a></li>
                <?php } ?>
            </ul>
        </nav>
    </header>