<?php
session_start();
$message = [];

if (!empty($_POST) && $_POST['authregister']) {
    validate_add();
}

function validate_add()
{
    global $message;
    $message = [];

    $data = [
        "name" => FILTER_SANITIZE_STRING,
        "phone" => FILTER_SANITIZE_NUMBER_FLOAT,
        "gender" => FILTER_SANITIZE_STRING,
        "email" => FILTER_VALIDATE_EMAIL,
        "password" => FILTER_SANITIZE_STRING
    ];

    // filter all input
    if ($s_data = filter_input_array(INPUT_POST, $data, false)) {
        $path = "database/users.txt";
        // Testing each input
        foreach ($s_data as $key => $input) {

            if (empty($input))
                $message[$key] = "Invalid input, Your $key is required";
        }

        // if their is no error message
        if (empty($message)) {

            $handle = fopen($path, 'a+');
            // if database folder and user.text has been created
            if (file_exists($path)) {

                // get all user from db 
                $users = file($path);

                //loop and check if user exist in our database
                foreach ($users as $user) {
                    $user = json_decode(base64_decode($user), true);
                    if ($user['email'] == $s_data['email'] || $user['phone'] == $s_data['phone']) {
                        $message['general'] = "User with this email or phone number Exist";
                        $_SESSION['message'] = $message;
                        session_write_close();
                        return;
                    }
                }
                // add new user
                fwrite($handle, base64_encode(json_encode($s_data)) . "\r\n");
                $message['success'] =  "Your Registration was successfully, kindly login.";
            } else {

                mkdir("database", 0777);
                fwrite($handle,  base64_encode(json_encode($s_data)) . "\r\n");
                $message['success'] =  "Your Registration was successfully, kindly login.";
            }
            fclose($handle);
        }
    } else {
        echo "<h1>Make sure you supplied all data!</h1>";
    }
    $_SESSION['message'] = $message;
}
include('header.php');
?>

<main>
    <h1 align="center">Registeration Page</h1>
    <h1>
        <?php
        
        if(isset($_SESSION['message'])){
            echo  (isset($_SESSION['message']['general']) ? $_SESSION['message']['general'] : (isset($_SESSION['message']['success']) ? $_SESSION['message']['success'] : ""));
        }
            
        ?>
    </h1>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">

        <div>
            <label for="name">Name</label><br>
            <input type="text" name="name" id="name" placeholder="Full name" required>
            <h3><?php
                echo isset($_SESSION['message']) ? (isset($_SESSION['message']['name']) ? $_SESSION['message']['name'] : "") : "";
                ?></h3>

        </div>

        <div>
            <label for="phone">Phone Number</label><br>
            <input type="tel" pattern="0[7-9]{1}[0,1]{1}[0-9]{8}" id="phone" name="phone" placeholder="E.g 08130447717" required>
            <h3><?php echo isset($_SESSION['message']) ? (isset($_SESSION['message']['phone']) ? $_SESSION['message']['phone'] : "") : "" ?></h3>
        </div>

        <div>
            <h2>Gender</h2>
            <input type="radio" name="gender" id="male" value="male" required>
            <label for="male">Male</label>

            <input type="radio" name="gender" id="female" value="female" required>
            <label for="female">Female</label>
            <h3><?php echo (isset($_SESSION['message']['gender']) ? $_SESSION['message']['gender'] : "") ?></h3>
        </div>

        <div>
            <label for="email">Email</label><br>
            <input type="email" name="email" id="email" required>
            <h3><?php echo (isset($_SESSION['message']['email']) ? $_SESSION['message']['email'] : "") ?></h3>
        </div>

        <div>
            <label for="password">Password</label><br>
            <input type="password" name="password" id="password">
            <h3><?php echo (isset($_SESSION['message']['password']) ? $_SESSION['message']['password'] : "") ?></h3>
        </div>

        <div>
            <input type="submit" name="authregister" value="Register"><br><br>
            <span>Already have an account? <a href="login.php">Login</a></span>
        </div>

    </form>
</main>
<?php

unset($_SESSION['message']);
$_SESSION['message'] = array();

?>

</body>

</html>