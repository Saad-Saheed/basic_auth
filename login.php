<?php
session_start();

$errmessage = [];

if (!empty($_POST) && $_POST['authlogin']) {
    validate_login();
}

function validate_login()
{
    global $errmessage;
    $errmessage = [];

    $data = [
        "email" => FILTER_VALIDATE_EMAIL,
        "password" => FILTER_SANITIZE_STRING
    ];

    // filter all input
    if ($s_data = filter_input_array(INPUT_POST, $data)) {
        $path = "database/users.txt";
        // Testing each input
        foreach ($s_data as $key => $input) {

            if (empty($input))
                $errmessage[$key] = "Invalid input, Your $key is required";
        }

        // if their is no error message
        if (empty($errmessage)) {
            // if database folder and user.text has been created
            if (file_exists($path) && filesize($path) > 0) {

                // get all user from db 
                $users = file($path);

                //loop and check if user exist in our database
                foreach ($users as $user) {
                    $user = json_decode(base64_decode($user), true);

                    if ((trim($s_data['email']) == trim($user['email'])) && ($s_data['password'] == $user['password'])) {
                        $_SESSION['current_user'] = $user;
                        header("location: index.php");
                    }
                }

                // no match
                $errmessage['general'] = "Invalid email or password, try again!";
                $_SESSION['errmessage'] = $errmessage;
            } else {

                die("Data Not Found!");
            }
        }
    } else {
        echo "<h1>Make sure you supplied all data!</h1>";
    }
    $_SESSION['errmessage'] = $errmessage;
}
include('header.php');
?>

<main>
    <h1 align="center">Login Page</h1>
    <h1>
        <?php
        echo (isset($_SESSION['errmessage']['general']) ? $_SESSION['errmessage']['general'] : "");
        unset($_SESSION['errmessage']['general']);
        ?>
    </h1>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">

        <div>
            <label for="email">Email</label><br>
            <input type="email" name="email" id="email" required>
            <h3><?php echo isset($_SESSION['errmessage']) ? (isset($_SESSION['errmessage']['email']) ? $_SESSION['errmessage']['email'] : "") : "" ?></h3>
        </div>
        <br>
        <div>
            <label for="password">Password</label><br>
            <input type="password" name="password" id="password" required>
            <h3><?php echo isset($_SESSION['errmessage']) ? (isset($_SESSION['errmessage']['password']) ? $_SESSION['errmessage']['password'] : "") : "" ?></h3>
        </div>
        <br>
        <div>
            <input type="submit" name="authlogin" value="Login"><br><br>
            <span>I am new here? <a href="register.php">Register</a></span><br><br>
            <span>Forget Password? <a href="reset_password.php">forget Password</a></span>

        </div>

    </form>
</main>


</body>

</html>