<?php
session_start();

$servername = "127.0.0.1";
$username = "Kenji";
$password = "JamesRyan";

$conn = mysqli_connect($servername, $username, $password, "violation_tracker");


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


$lockout_time = 30;

$servername = "127.0.0.1";
$username = "Kenji";
$password = "JamesRyan";

$conn = mysqli_connect($servername, $username, $password, "violation_tracker");




if (isset($_POST["reset-password"]) && !isset($_GET["code"])) {
    $old_password = $_POST["old_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    $sql = "SELECT * FROM users WHERE username = 'staff'";

    $result = mysqli_query($conn, $sql);

    $user;

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
    }


    $stored_password = $user["password"];

    $_SESSION["old_password"] = $old_password;
    $_SESSION["new_password"] = $new_password;
    $_SESSION["confirm_password"] = $confirm_password;

    // Validate old password
    if (!password_verify($old_password, $stored_password)) {
        $_SESSION['error'] = "Incorrect ang imong old nga password.";
        header("Location: /CTU-Violation/reset_staff_password.php"); // Redirect back to form
        exit;
    }

    // Validate new password
    if (strlen($new_password) < 8) {
        $_SESSION['error'] = "New password must be at least 8 characters long.";
        header("Location: /CTU-Violation/reset_staff_password.php");
        exit;
    }
    if (!preg_match('/[A-Z]/', $new_password)) {
        $_SESSION['error'] = "New password must contain at least one uppercase letter.";
        header("Location: /CTU-Violation/reset_staff_password.php");
        exit;
    }
    if (!preg_match('/[a-z]/', $new_password)) {
        $_SESSION['error'] = "New password must contain at least one lowercase letter.";
        header("Location: /CTU-Violation/reset_staff_password.php");
        exit;
    }
    if (!preg_match('/\d/', $new_password)) {
        $_SESSION['error'] = "New password must contain at least one numeric digit.";
        header("Location: /CTU-Violation/reset_staff_password.php");
        exit;
    }
    if (!preg_match('/[\W_]/', $new_password)) {
        $_SESSION['error'] = "New password must contain at least one special character.";
        header("Location: /CTU-Violation/reset_staff_password.php");
        exit;
    }
    if ($new_password === $old_password) {
        $_SESSION['error'] = "New password must not be the same as the old password.";
        header("Location: /CTU-Violation/reset_staff_password.php");
        exit;
    }

    // Validate confirm password
    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "Confirm password must match the new password.";
        header("Location: /CTU-Violation/reset_staff_password.php");
        exit;
    } else {
        $ang_password = password_hash($new_password, PASSWORD_BCRYPT);

        $updateQuery = "UPDATE users SET password = '$ang_password' WHERE username = 'staff'";



        if (mysqli_query($conn, $updateQuery))
            $_SESSION["form_message"] = "Password updated successfully!";
    }


    unset($_SESSION["error"]);

    unset($_SESSION["old_password"]);
    unset($_SESSION["new_password"]);
    unset($_SESSION["confirm_password"]);

    // Display errors or success message
    header("Location: /CTU-Violation/");

    return; // para ma break ang flow; dili molahos sa ubos
}



if (isset($_GET["code"])) {
    $token = $_GET["code"];

    $sql = "SELECT * FROM reset_code WHERE code = '$token'";

    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $matched_token = mysqli_fetch_assoc($result);

        if ($matched_token["is_used"]) {
            $_SESSION["form_message"] = "Token expired.";
            header("Location: /CTU-Violation");
        }
    }
}

if (isset($_POST["reset-password"]) && isset($_GET["code"])) {
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    $_SESSION["new_password"] = $new_password;
    $_SESSION["confirm_password"] = $confirm_password;
    // Validate new password
    if (strlen($new_password) < 8) {
        $_SESSION['error'] = "New password must be at least 8 characters long.";
        header("Location: /CTU-Violation/reset_staff_password.php?code=" . $_GET["code"]);
        exit;
    }
    if (!preg_match('/[A-Z]/', $new_password)) {
        $_SESSION['error'] = "New password must contain at least one uppercase letter.";
        header("Location: /CTU-Violation/reset_staff_password.php?code=" . $_GET["code"]);
        exit;
    }
    if (!preg_match('/[a-z]/', $new_password)) {
        $_SESSION['error'] = "New password must contain at least one lowercase letter.";
        header("Location: /CTU-Violation/reset_staff_password.php?code=" . $_GET["code"]);
        exit;
    }
    if (!preg_match('/\d/', $new_password)) {
        $_SESSION['error'] = "New password must contain at least one numeric digit.";
        header("Location: /CTU-Violation/reset_staff_password.php?code=" . $_GET["code"]);
        exit;
    }
    if (!preg_match('/[\W_]/', $new_password)) {
        $_SESSION['error'] = "New password must contain at least one special character.";
        header("Location: /CTU-Violation/reset_staff_password.php?code=" . $_GET["code"]);
        exit;
    }
    if ($new_password === $old_password) {
        $_SESSION['error'] = "New password must not be the same as the old password.";
        header("Location: /CTU-Violation/reset_staff_password.php?code=" . $_GET["code"]);
        exit;
    }

    $ang_password = password_hash($new_password, PASSWORD_BCRYPT);

    $updateQuery = "UPDATE users SET password = '$ang_password' WHERE username = 'staff'";



    if (mysqli_query($conn, $updateQuery))
        $_SESSION["form_message"] = "Password updated successfully!";

    unset($_SESSION["error"]);

    unset($_SESSION["old_password"]);
    unset($_SESSION["new_password"]);
    unset($_SESSION["confirm_password"]);


    header("Location: /CTU-Violation/");

    return;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="waidth=device-width, initial-scale=1.0">
    <title>Document</title>


    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {

            min-height: 100vh;
            background-color: #ededed;

            display: flex;
            justify-content: center;
            align-items: center;

            flex-direction: column;

            /* color: #f8f8f8; */
        }

        /* background: black;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    width: 400px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
    position: relative;
    transition: all 0.3s ease; */

        .alert {
            background: black;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            width: 400px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            color: #f8f8f8;
        }

        .btn {
            text-decoration: none;
            color: #ffffff;
            border-radius: 10px;
            /* font-size: 1.3rem;s */
            display: inline-block;
            background: #FA9800;
            padding: 10px 30px;
            transition: background-color 0.3s ease;
            position: relative;
            z-index: 10;
        }

        .input {
            width: 320px;
            padding: 10px 15px;
            margin: 10px 0;
            background-color: #ffffff;
            color: #000000;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            font-size: 20px;
            outline: none;
            transition: border 0.3s ease;
            filter: drop-shadow(0px 4px 20px rgba(0, 0, 0, 0.25));
        }
    </style>
</head>

<body>
    <?php if (isset($_SESSION["error_message"])): ?>
        <div class="alert">
            <?php echo $_SESSION["error_message"]; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION["error"])): ?>
        <div class="alert">
            <?php echo $_SESSION["error"]; ?>
        </div>
    <?php endif; ?>
    <br>
    <form
        action="/CTU-Violation/reset_staff_password.php?<?php echo isset($_GET['code']) ? 'code=' . $_GET['code'] : '' ?>"
        method="POST">



        <?php if (!isset($_GET["code"])): ?>
            <div>
                <label for="">Old Password</label>
                <br>
                <input type="password" class="input" name="old_password"
                    value="<?php echo $_SESSION['old_password'] ?? '' ?>">
            </div>
        <?php endif; ?>

        <div>
            <label for="">New Password</label>
            <br>
            <input type="password" class="input" name="new_password"
                value="<?php echo $_SESSION['new_password'] ?? '' ?>">
        </div>

        <div>
            <label for="">Confirm New Password</label>
            <br>
            <input type="password" class="input" name="confirm_password"
                value="<?php echo $_SESSION['confirm_password'] ?? '' ?>">
        </div>
        <br>

        <input type="submit" value="Reset" name="reset-password" class="btn">

        <br>
        <br>

        <a href="/CTU-Violation/forgot_password.php?user=staff">Forgot Password?</a>
    </form>
</body>

</html>

<?php
unset($_SESSION["error"]);

unset($_SESSION["old_password"]);
unset($_SESSION["new_password"]);
unset($_SESSION["confirm_password"]);

if (isset($_SESSION['last_staff_attempt_time']) && time() - $_SESSION['last_staff_attempt_time'] < $lockout_time)
    unset($_SESSION["error_message"]);
?>