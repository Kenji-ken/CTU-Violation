<?php session_start() ?>
<!--mao nani -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Violation</title>
    <link rel="stylesheet" href="style.css">
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    </head>
<body>

    <?php if (isset($_SESSION["form_message"])): ?>
        <div class="alert">
            <?php echo $_SESSION["form_message"] ?>
        </div>
    <?php endif ?>

    <div class="container">
    <div class="warning-banner">
    <div><img src="main/images/logo.png" class="nav-logo"></div>
        <div class="admin-button"><a href="#" id="admin-btn"><img src="main/images/admin-icon2.png" class="admin-icon"><span>Admin</span></a></div>
    </div>

    <div class="content">
         <div class="logo-name">
            <div class="upper-text"><h1>VIOLATI<img src="main/images/timer2.png" class="timer">N</h1></div>
            <div class="lower-text">TRACKER</div>
        </div>

        <!-- Updated the Appeal button to a search form for Student ID -->
        <form id="search-student-form">
            <input type="text" id="student-id" class="student-id-text" placeholder="Enter Student ID" required>
            <button type="submit" class="button pulse" id="search-btn"><img src="main/images/search-icon3.png" class="search-icon"></button>
        </form>

        <div class="button-group">
           
            <a href="#" class="button staff-button" id="staff-btn">Report Violation</a> <!-- Staff button -->
        </div>
    </div>
    <div class="student-manual-btn"><a href="student_Manual.html"><img src="main/images/manual.png" class="manual" id="s-manual">Student Manual</a></div>
    <div class="background-overlay"></div>

    <!-- Full Screen Warning Icon Overlay -->
    <div id="warning-overlay" class="warning-overlay">
        ⚠️
        <p>Redirecting...</p>
    </div>

    <!-- Admin Login Modal (unchanged) -->
  <div id="adminModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeAdmin">&times;</span> 
        <img src="main/images/logo.png" alt="Logo" class="logo">
        <h3>Login Admin</h3>
        <form action="admin.php" method="post">
            <label for="username">Username</label>
            <input type="text" id="admin-username" name="username" placeholder="Admin" required>
            
            <label for="password">Password</label>
            <div class="password-container">
                <input type="password" id="admin-password" name="password" placeholder="Password" required>
                <i class="fa fa-eye toggle-password" onclick="togglePasswordVisibility('admin-password', this)"></i>
            </div>
            
            <button type="submit" class="login-btn" id="modal-btn">Login</button>
        </form>
    </div>
</div>

<!-- Staff Login Modal (with Forgot Password) -->

<div id="staffModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeStaff">&times;</span>
        <img src="main/images/logo.png" alt="School Logo" class="logo">
        <h3>Login Staff</h3>
        <form action="staff.php" method="post">
            <label for="username">Username</label>
            <input type="text" id="staff-username" name="username" placeholder="Staff" required>
            
            <label for="password">Password</label>
            <div class="password-container">
                <input type="password" id="staff-password" name="password" placeholder="Password" required>
                <i class="fa fa-eye toggle-password" onclick="togglePasswordVisibility('staff-password', this)"></i>
            </div>
            
            <button type="submit" class="login-btn" id="modal-btn">Login</button>
        </form>
        <a href="#" id="forgotStaffPassword">Forgot Password?</a>
    </div>
</div>

<!-- Forgot Password Modal -->
<div id="forgotPasswordModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeForgotPasswordModal">&times;</span>
        <h3>Forgot Password</h3>
        <form id="forgotPasswordForm" action="forgot_password.php" method="post">
            <label for="email">Enter your Email</label>
            <input type="email" id="forgot-password-email" name="email" placeholder="Email" required>
            <button type="submit" class="login-btn">Send Verification Code</button>
        </form>
    </div>
</div>

<!-- Reset Password Modal -->
<div id="resetPasswordModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeResetPasswordModal">&times;</span>
        <h3>Reset Password</h3>
        <form id="resetPasswordForm" action="reset_password.php" method="post">
            <input type="hidden" name="email" id="reset-email">
            <label for="verification-code">Verification Code</label>
            <input type="text" id="verification-code" name="verification_code" placeholder="Enter Code" required>

            <label for="new-password">New Password</label>
            <input type="password" id="new-password" name="new_password" placeholder="New Password" required>

            <label for="confirm-password">Confirm Password</label>
            <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm Password" required>

            <button type="submit" class="login-btn">Reset Password</button>
        </form>
    </div>
</div>

<!-- Violation Record Modal -->
<div id="violationModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeViolationModal">&times;</span>
        <div id="violation-details">
            <!-- Content populated by JavaScript -->
        </div>
    </div>
</div>
</div>

    <!-- JavaScript -->
    <script src="script.js"></script>
    <script>
function togglePasswordVisibility(passwordFieldId, toggleIconElement) {
    const passwordField = document.getElementById(passwordFieldId);
    if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleIconElement.classList.remove("fa-eye");
        toggleIconElement.classList.add("fa-eye-slash");  // Show crossed-out eye
    } else {
        passwordField.type = "password";
        toggleIconElement.classList.remove("fa-eye-slash");
        toggleIconElement.classList.add("fa-eye");  // Show open eye
    }
}

function setupPasswordToggle(passwordFieldId, toggleIconElement) {
    const passwordField = document.getElementById(passwordFieldId);

    // Show icon only when there is text in the password field
    passwordField.addEventListener("input", () => {
        toggleIconElement.style.visibility = passwordField.value ? "visible" : "hidden";
    });

    // Initially hide the icon
    toggleIconElement.style.visibility = "hidden";
}

// Call setup function for each password field and corresponding icon
setupPasswordToggle("admin-password", document.querySelector("#adminModal .toggle-password"));
setupPasswordToggle("staff-password", document.querySelector("#staffModal .toggle-password"));

</script>
</body>

</html>

<?php unset($_SESSION["form_message"])?>