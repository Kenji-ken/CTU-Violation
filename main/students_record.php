<?php
session_start();
include './auth.php';

checkAccess('admin'); // Restrict to admin only
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Violation Tracking System</title>
    <link rel="stylesheet" href="Sstyles.css">
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">


    <style>
        .timer {
            text-align: center;
        }
        .sort-search {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }    
        
    </style>
</head>
<body>
    
<a href="admin_logout.php">Logout</a>
<!-- 
<script>
function logoutAdmin() {
    // Send the logout request to the server to destroy the session
    fetch('admin_logout.php')  // Make sure the URL is correct
        .then(response => response.json())  // Parse the response from PHP
        .then(data => {
            if (data.status === 'logged_out') {
                // Set the 'adminLoggedOut' flag in localStorage to notify other tabs
                localStorage.setItem('adminLoggedOut', 'true');
                
                // Redirect to the login page (index.php or wherever your login page is)
                window.location.href = 'index.php';  // Adjust the URL as needed
            }
        })
        .catch(error => {
            console.error('Error during logout:', error);
        });
}
</script> -->




    <div class="container">

    <div class="container">
        <button class="hamburger" id="hamburger">&#9776;</button> <!-- Hamburger button -->
        <aside class="sidebar">
            <div class="sidebar-nav">
                <img src="images/logo.png">
                <h3>Violation Tracker</h3>
            </div>
            <ul>
                <li><a href="#" class="active">COT Pending Records</a></li>
                <li><a href="cot_history_records.php" class="active">History</a></li>
                <li><a href="#">COE Students Records</a></li>
            </ul>
        </aside>


        <main class="content" style="overflow-x: auto; max-height: 100vh;">>
            <h2>Student Records</h2>
            <div class="sort-search">
                <div class="search-container">
                    <input type="text" id="search" placeholder="Search by ID or Last Name..." class="search-bar" />
                    <button id="cancelBtn" class="cancel-btn" onclick="document.getElementById('search').value=''">&times;</button>
                    <button id="searchBtn">Search</button>
                </div>

                <div class="sorting-controls">
                    <label for="sortDropdown">Sort by: </label>
                    <select id="sortDropdown" onchange="handleSortDropdown()">
                    <option value="">-Select-</option>
                    <option value="0-number">ID</option>
                    <option value="1-string">Last Name</option>
                    <option value="6-date">Date and Time</option>
                    </select>
                </div>
            </div>

            <table>
              <thead>
                <tr>
                <th>ID</th>
                <th>Lastname</th>
                <th>Firstname</th>
                <th>Violation</th>
                <th>Offense</th>
                <th>Sanction</th>
                <th>Date and Time</th>
                <th>Image</th> <!-- Added Image Column -->
                <th>Actions</th>
                <th>Timer</th>
            </tr>
        </thead>
        <tbody id="student-table-body">
            <?php
            include 'formatTime.php'; // Adjust the path if needed

            // Connect to the database
            $servername = "127.0.0.1";
            $username = "Kenji";
            $password = "JamesRyan";
            $dbname = "violation_tracker";

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch all violations
            $sql = "SELECT * FROM violations";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    echo "<tr data-id='" . htmlspecialchars($row['student_id']) . "'>";
                    echo "<td>" . htmlspecialchars($row['student_id']) . "</td>";
                    echo "<td class='lastname'>" . htmlspecialchars($row['lastname']) . "</td>";
                    echo "<td class='firstname'>" . htmlspecialchars($row['firstname']) . "</td>";

                    // Check if 'violation' key exists in the array
                    $violation = isset($row['violation']) ? htmlspecialchars($row['violation']) : 'N/A';
                    echo "<td class='violation'>" . $violation . "</td>";

                    // Fetch offense and sanction from the database
                    echo "<td class='offense'>" . htmlspecialchars($row['offense'] ?? 'N/A') . "</td>";
                    echo "<td class='sanction'>" . htmlspecialchars($row['sanction'] ?? 'N/A') . "</td>";

                    echo "<td class='timestamp'>" . htmlspecialchars($row['timestamp']) . "</td>";
                

                    // Handle image URL with fallback
                    $imageUrl = !empty($row['image_url']) ? htmlspecialchars($row['image_url']) : 'ID/Students/default.jpg'; // Replace with your default image path
                    echo "<td><img class='profile-image' src='" . $imageUrl . "' alt='Profile Image' width='50' /></td>";

                    echo "<td>
                            <button class='edit-btn' data-id='" . htmlspecialchars($row['student_id']) . "'>View</button>
                            <button class='delete-btn' data-id='" . htmlspecialchars($row['student_id']) . "'>Done</button>
                          </td>";
                    echo "<td class='timer' data-id='" . $row['id'] . "' data-time='" . ($row['end_time']) .
                        "' data-cycle='" . $row['cycle'] . "'>" .
                        0 .
                        "</td>"; // Timer Cell
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='12'>No records found</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</div>

        </main>
    </div>
    <script>
           // Function to reload the page when cancel button is clicked
           document.getElementById('cancelBtn').addEventListener('click', function() {
            window.location.reload();
        });

        // Function to open the modal
        function openModal() {
            const modal = document.getElementById("profileModal");
            modal.style.display = "flex";
        }

        // Function to close the modal
        function closeModal() {
            const modal = document.getElementById("profileModal");
            modal.style.display = "none";
        }

        // Event listener for edit buttons and delete buttons
        document.addEventListener("DOMContentLoaded", () => {
            // Edit button functionality
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', (event) => {
                    const row = event.target.closest('tr');
                    const studentId = row.getAttribute('data-id');

                    // Get student data from the row
                    const studentName = row.querySelector('.firstname').textContent + ' ' + row.querySelector('.lastname').textContent;
                    const violation = row.querySelector('.violation').textContent;
                    const offense = row.querySelector('.offense').textContent;
                    const sanction = row.querySelector('.sanction').textContent;
                    const timestamp = row.querySelector('.timestamp').textContent;
                    const imageUrl = row.querySelector('.profile-image').src; // Get image URL

                    // Populate modal with student data
                    document.getElementById('student-id').textContent = studentId;
                    document.getElementById('student-name').textContent = studentName;
                    document.getElementById('violation').textContent = violation;
                    document.getElementById('offense').textContent = offense;
                    document.getElementById('sanction').textContent = sanction;
                    document.getElementById('time').textContent = timestamp;

                    // Populate course and department
                    document.getElementById('student-course').textContent = row.querySelector('.course').textContent;
                    document.getElementById('student-department').textContent = row.querySelector('.department').textContent;

                    // Set the image in the modal
                    document.getElementById('student-image').src = imageUrl; // Update image in modal

                    // Open the modal
                    openModal();
                });
            });

            // Delete button functionality
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', (event) => {
                    const studentId = event.target.getAttribute('data-id');

                    if (confirm('Are you sure you want to delete this record?')) {
                        fetch('delete_student.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: 'student_id=' + encodeURIComponent(studentId)
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        alert('Student record deleted successfully');
        location.reload(); // Reload the page to reflect changes
    } else {
        alert('Error deleting record: ' + (data.error || 'Unknown error'));
    }
})
.catch(error => {
    console.error('Error:', error);
    alert('An error occurred while deleting the record: ' + error.message);
});

                    }
                });
            });

            // Close modal functionality
            document.querySelector('.close').addEventListener('click', closeModal);

            // Close the modal when clicking outside the content
            window.onclick = function(event) {
                const modal = document.getElementById("profileModal");
                if (event.target === modal) {
                    closeModal();
                }
            };
        });
    </script>
    
    <script>
document.addEventListener('DOMContentLoaded', function () {
    // Fetch the student's violation history and populate the modal
    function loadViolationHistory(studentId) {
        fetch('get_violation_history.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'student_id=' + encodeURIComponent(studentId)
        })
        .then(response => response.json())
        .then(data => {
            const historyBody = document.getElementById('violation-history-body');
            historyBody.innerHTML = ''; // Clear existing rows
            
            data.forEach(record => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${record.violation}</td>
                    <td>${record.offense}</td>
                    <td>${record.sanction}</td>
                    <td>${record.timestamp}</td>
                `;
                historyBody.appendChild(row);
            });
        });
    }

    // Call loadViolationHistory() inside your edit button event listener
    // document.querySelectorAll('.edit-btn').forEach(button => {
    //     button.addEventListener('click', (event) => {
    //         const studentId = event.target.closest('tr').getAttribute('data-id');
    //         loadViolationHistory(studentId); // Load violation history for this student
    //     });
    // });

    document.addEventListener("click", evt => {
        if (evt.target.matches("button[data-id]")) {
            const studentId = evt.target.getAttribute("data-id");
            console.log(studentId)
            loadViolationHistory(studentId); // Load violation history for this student
        }
    })
});
</script>

    <!-- Modal Structure -->
    <div id="profileModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="profile-container">
                <div class="header">
                    <h3>Student Profile</h3>
                </div>
                <div class="profile-details">
                    <div class="profile-picture">
                        <img src="https://via.placeholder.com/100" alt="Profile Picture" id="student-image">
                    </div>
                    <div class="profile-info">
                        <p><strong>ID Number:</strong> <span id="student-id">1234567</span></p>
                        <p><strong>Name:</strong> <span id="student-name">Juan DeLa Cruz</span></p>
                        <p><strong>Course:</strong> <span id="student-course">Information Technology</span></p>
                        <p><strong>Department:</strong> <span id="student-department">Technology</span></p>
                    </div>
                </div>

                <div class="violation-section">
                    <table>
                        <tr>
                            <th>Violation</th>
                            <th>Offense</th>
                            <th>Sanction</th>
                            <th>Date and Time</th>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" id="violation" readonly> <!-- Change this to an input field for exact violation -->
                            </td>
                            <td><input type="text" id="offense" value="1st Offense" readonly></td>
                            <td><input type="text" id="sanction" value="Warning" readonly></td>
                            <td><input type="text" id="time" readonly></td> <!-- Keep this as readonly to show date/time -->
                        </tr>
                    </table>
                </div>
                <div class="violation-history" style="overflow-x: auto; max-height: 200px; border: 1px solid #ccc;">
    <h3>Violation History</h3>
    <table>
        <thead>
            <tr>
                <th>Violation</th>
                <th>Offense</th>
                <th>Sanction</th>
                <th>Date and Time</th>
            </tr>
        </thead>
        <tbody id="violation-history-body">
            <!-- Violation history rows will be appended here dynamically -->
        </tbody>
    </table>
</div>

                <div class="buttons">
                    <button class="print">Print</button>
                    <button class="cancel">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this code inside the body tag of students_record.php -->

 <!-- Staff Login Modal (unchanged) -->
 <div id="staffModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeStaff">&times;</span>
            <img src="images/Logo.png" alt="School Logo" class="school-logo">
            <h2>E-Logbook</h2>
            <h3>Login Staff</h3>
            <form action="staff.php" method="post">
                <label for="username">Username</label>
                <input type="text" id="staff-username" name="username" placeholder="Staff" required>
                
                <label for="password">Password</label>
                <input type="password" id="staff-password" name="password" placeholder="Password" required>
                
                <button type="submit" class="login-btn">Login</button>
            </form>
        </div>
    </div>
        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <p><?php echo $error_message; ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Open the staff modal when dashboard is clicked
    const dashboardLink = document.querySelector('a[href="index.php"]');
    const staffModal = document.getElementById('staffModal');

    dashboardLink.addEventListener('click', (e) => {
        e.preventDefault(); // Prevent the default link behavior
        staffModal.style.display = 'block'; // Show the staff modal
    });

    // Close staff modal
    document.getElementById('closeStaff').onclick = function() {
        staffModal.style.display = 'none';
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        if (event.target == staffModal) {
            staffModal.style.display = 'none';
        }
    }
});
</script>
<?php
// Sample query to fetch student violation time

if (isset($_POST['student_id'])) {
    $student_id = $_POST['student_id']; // Assuming you pass the student ID via POST

// Query the database to get the student's violation data
$query = "SELECT firstname, lastname, violation_time FROM violation_history WHERE student_id = '$student_id'";
$result = mysqli_query($connection, $query);

if(mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    // Send the data back as JSON, including the time
    echo json_encode([
        'firstname' => $row['firstname'],
        'lastname' => $row['lastname'],
        'violation_time' => $row['violation_time'], // This could be a countdown or timestamp
    ]);
} else {
    echo json_encode(['error' => 'No record found']);
}
}

?>


    <script src="script.js"></script>

    <script>
    const searchInput = document.getElementById("search");
const cancelBtn = document.getElementById("cancelBtn");

// Initially hide the cancel button
cancelBtn.style.display = "none";

// Show the cancel button only when there is input
searchInput.addEventListener("input", () => {
    if (searchInput.value.trim() !== "") {
        cancelBtn.style.display = "block";
    } else {
        cancelBtn.style.display = "none";
    }
});

// Clear the input field when cancel button is clicked
function clearSearch() {
    searchInput.value = "";
    cancelBtn.style.display = "none";
    searchInput.focus();
}
 </script>

<script>
//search-button == enter-key
document.addEventListener('keydown', function(event) {
    // Check if the Enter key is pressed
    if (event.key === 'Enter') {
        // Trigger the "Next" button's click event
        document.getElementById('searchBtn').click();
    }
});


document.addEventListener('DOMContentLoaded', () => {
    const hamburger = document.getElementById('hamburger');
    const sidebar = document.querySelector('.sidebar');

    // Toggle sidebar visibility
    hamburger.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });

    // Close sidebar when clicking outside of it (optional)
    window.addEventListener('click', (event) => {
        if (!sidebar.contains(event.target) && !hamburger.contains(event.target)) {
            sidebar.classList.remove('active');
        }
    });
});

</script>
<script>
 function logout() {
    // Optional: Send a logout request to PHP
    window.location.href = 'logout.php'; // Redirect to logout.php
}
// Listen for logout event across tabs
window.addEventListener('storage', function(e) {
    if (e.key === 'logout' && e.newValue === 'true') {
        window.location.href = '../index.php'; // Redirect to login page
    }
});

</script>
<script>
// Function to handle logout synchronization across tabs
function syncLogoutAcrossTabs() {
    // Listen for changes to `localStorage` (logout trigger)
    window.addEventListener('storage', function(event) {
        if (event.key === 'adminLoggedOut' && event.newValue === 'true') {
            // Redirect to the login page when logout is triggered in another tab
            window.location.href = 'index.php'; // Make sure this redirects to the login page
        }
    });

    // Check if the admin has logged out (based on a flag in localStorage)
    if (localStorage.getItem('adminLoggedOut') === 'true') {
        // Remove the flag to prevent redundant logout
        localStorage.removeItem('adminLoggedOut');
        // Redirect to login page immediately
        window.location.href = 'index.php'; // Adjust the URL as needed
    }
}

// Call the function when the page loads to sync the logout status if needed
syncLogoutAcrossTabs();
</script>

</body>
</html>
