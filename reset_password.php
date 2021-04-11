<?php
ini_set("session.cookie_lifetime", 1200);
session_start();

$errmessage = [];

if (!empty($_POST) && $_POST['authreset']) {
    validate_reset();
}

function validate_reset()
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
        // create file handler
        $handle = fopen($path, 'r+');

        $found = false;
        // Testing each input
        foreach ($s_data as $key => $input) {

            if (empty($input))
                $errmessage[$key] = "Invalid input, Your $key is required";
        }

        // if their is no error message
        if (empty($errmessage)) {
            // if database folder and user.text has been created
            if (file_exists($path) && filesize($path) > 0) {


                $users = [];
                //loop through each line in our database
                while (!feof($handle)) {
                    $user = json_decode(base64_decode(fgets($handle)), true);

                    if (isset($user) && trim($s_data["email"]) == trim($user["email"])) {

                        // change Password
                        $user["password"] = $s_data["password"];
                        //if email Found
                        $found = true;
                        // header("location: login.php");
                    }

                    isset($user) ? ($users[] = $user) : "";
                }
                if ($found) {

                    // echo "<pre>";
                    // print_r($users);
                    // echo "</pre>";
                    file_put_contents($path, "");
                    // re write all datas to the file and lock the file while adding datas
                    foreach ($users as $this_user) {
                        file_put_contents($path, base64_encode(json_encode($this_user)) . "\r\n", LOCK_EX | FILE_APPEND);
                    }

                    $errmessage['general'] = "Password change successfully, kindly login with your new password! " . '<a href="login.php">here</a>';
                } else {
                    // no match
                    $errmessage['general'] = "Invalid email, try again!";
                }
                fclose($handle);
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
    <h1 align="center">Reset Password</h1>
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
            <label for="password">New Password</label><br>
            <input type="password" name="password" id="password" required>
            <h3><?php echo isset($_SESSION['errmessage']) ? (isset($_SESSION['errmessage']['password']) ? $_SESSION['errmessage']['password'] : "") : "" ?></h3>
        </div>
        <br>
        <div>
            <input type="submit" name="authreset" value="Change Password"><br>
            <span>I just remember? <a href="login.php">Login</a></span>

        </div>

    </form>
</main>


</body>

</html>