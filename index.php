<?php
session_start();

include('header.php');
if (!isset($_SESSION['current_user']))
    header("location: login.php");
?>

<main>
    <h1 align="center">Home Page</h1>
    <h1>Your are welcome dear <?php echo $user->name ?></h1>
</main>


</body>

</html>