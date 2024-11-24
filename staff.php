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

// Track staff login attempts
if (!isset($_SESSION['staff_login_attempts'])) {
    $_SESSION['staff_login_attempts'] = 0;
}

$max_attempts = 5;
$lockout_time = 30; // 30 seconds lockout

// Check if the account is locked
if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($error_message)) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT password, role FROM users WHERE username = ?");
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($hashed_password, $role); // Fetch password and role
            $stmt->fetch();

            // Verify password
            if (password_verify($password, $hashed_password)) {
                // Assign role-based redirection logic
                if ($role === 'staff') {
                    $_SESSION['staff'] = [
                        'username' => $username,
                        'role' => 'staff',
                    ];
                    $_SESSION['logged_in'] = true;
                    $_SESSION['role'] = 'staff';
                    
                    header("Location: main/index.php");
                    exit;
                    
                }
            }
        }

        // Handle failed login attempts
        $_SESSION['staff_login_attempts'] += 1;
        $_SESSION['last_staff_attempt_time'] = time();
        $error_message = "Invalid username or password. Attempts remaining: " . ($max_attempts - $_SESSION['staff_login_attempts']);
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

    <?php if ($_SESSION['staff_login_attempts'] >= $max_attempts && time() - $_SESSION['last_staff_attempt_time'] < $lockout_time) {
    }?>
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
