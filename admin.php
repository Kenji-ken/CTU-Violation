<?php
session_start();
$servername = "127.0.0.1"; // Your server name
$username = "Kenji"; // Your database username
$password = "JamesRyan"; // Your database password
$dbname = "violation_tracker"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$error_message = "";

// Track admin login attempts
if (!isset($_SESSION['admin_login_attempts'])) {
    $_SESSION['admin_login_attempts'] = 0;
}

$max_attempts = 5;
$lockout_time = 30; // 30 seconds lockout

// Check if the account is locked
if ($_SESSION['admin_login_attempts'] >= $max_attempts) {
    if (time() - $_SESSION['last_admin_attempt_time'] < $lockout_time) {
        $error_message = "Too many failed attempts. Please try again later.";
        $_SESSION["error_message"] = $error_message;
        
        header("Location: /CTU-Violation/reset_admin_password.php");
    } else {
        $_SESSION['admin_login_attempts'] = 0; // Reset attempts after lockout period
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($error_message)) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ? AND role = 'admin'");

    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            // Fetch the hashed password
            $stmt->bind_result($hashed_password);
            $stmt->fetch();

            // Verify the password
            if (password_verify($password, $hashed_password)) {
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'admin';
                header("Location: main/students_record.php");
                exit;
            }
        }

        // Failed login, increase attempt count
        $_SESSION['admin_login_attempts']++;
        $_SESSION['last_admin_attempt_time'] = time();
        $error_message = "Invalid username or password. Attempts remaining: " . ($max_attempts - $_SESSION['admin_login_attempts']);

        $error_message = "
        <div style='display: flex; align-items: center; gap: 10px; justify-content: center;'>
            <img src='main/images/error-icon.png' alt='Error Icon' style='width: 30px; height: 30px; border-radius: 0;'>
            <div>
                <p style='color: red; margin: 0;'>Invalid username or password.</p>
                <p style='margin: 0;'>Attempts remaining: " . ($max_attempts - $_SESSION['admin_login_attempts']) . "</p>
            </div>
        </div>";
        

        // Close the statement
        $stmt->close();
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css"> <!-- Your CSS file -->
</head>
<body>

<?php if (!empty($error_message)): ?>
    <div id="errorModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p><?php echo $error_message; ?></p>
        </div>
    </div>
<?php endif; ?>

<script>
// Get the modal
var modal = document.getElementById("errorModal");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// Show the modal if it exists
if (modal) {
    modal.style.display = "flex";
}

// When the user clicks on <span> (x), close the modal and redirect
span.onclick = function() {
    modal.style.display = "none";
    window.location.href = "index.php"; // Redirect to login page
}

// When the user clicks anywhere outside of the modal, close it and redirect
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
        window.location.href = "index.php"; // Redirect to login page
    }
}
</script>

</body>
</html>
