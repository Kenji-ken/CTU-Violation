<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COT History of Violation Records</title>
    <link rel="stylesheet" href="Sstyles.css">

    

</head>
<body>
    
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-nav">
                <img src="images/logo.png">
                <h3>VIolation Tracker</h3>
            </div>
            <ul>
                <a href="index.php" id="dashboardLink">Dashboard</a>
                <li><a href="students_record.php">COT Pending Records</a></li>
                <li><a href="#" class="active">COT History of Violation Records</a></li>
                <li><a href="#">COE Students Records</a></li>
            </ul>
        </aside>

        <main class="content">
            <h2>COT History of Violation Records</h2>

            <!-- Search Bar -->
            <div class="search-container">
                <input type="text" id="search" placeholder="Search by ID or Last Name..." class="search-bar" />
                <button id="cancelBtn" class="cancel-btn" onclick="document.getElementById('search').value=''">&times;</button>
                <button id="searchBtn">Search</button>
            </div>

            <div style="overflow-x: auto; max-height: 400px; border: 1px solid #ccc;">
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
                    </tr>
                </thead>
                <tbody id="history-table-body">
                <?php
                    include 'db/connection.php';

                    if (!$conn) {
                        die("Connection failed: " . mysqli_connect_error());
                    }

                    $sql = "SELECT vh.*, s.lastname, s.firstname, s.profile_image 
                            FROM violation_history vh 
                            LEFT JOIN students s ON vh.student_id = s.student_id
                            WHERE vh.student_id IS NOT NULL";

                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr data-id='" . htmlspecialchars($row['student_id']) . "'>";
                            echo "<td>" . htmlspecialchars($row['student_id']) . "</td>";
                            echo "<td class='lastname'>" . htmlspecialchars($row['lastname']) . "</td>";
                            echo "<td class='firstname'>" . htmlspecialchars($row['firstname']) . "</td>";
                            echo "<td class='violation'>" . htmlspecialchars($row['violation']) . "</td>";
                            echo "<td class='offense'>" . htmlspecialchars($row['offense']) . "</td>";
                            echo "<td class='sanction'>" . htmlspecialchars($row['sanction']) . "</td>";
                            echo "<td class='timestamp'>" . htmlspecialchars($row['timestamp']) . "</td>";
                            echo "<td><img class='profile-image' src='" . htmlspecialchars($row['profile_image']) . "' alt='Profile Image' width='50' /></td>";
                            echo "<td>
                            <button class='edit-btn' data-id='" . htmlspecialchars($row['student_id']) . "'>Edit</button>
                            <button class='delete-btn' data-id='" . htmlspecialchars($row['student_id']) . "'>Delete</button>
                          </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='11'>No records found</td></tr>";
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
            modal.style.display = "block";
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
        
        // Get image URL and set it in the modal (Add a placeholder if image is missing)
        const imageUrl = row.querySelector('.profile-image').src;
        const studentImage = document.getElementById('student-image');
        studentImage.src = imageUrl !== '' ? imageUrl : 'https://via.placeholder.com/100'; // Use placeholder if no image

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

        // Open the modal
        openModal();
    });
});


            // Delete button functionality
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', (event) => {
                    const studentId = event.target.getAttribute('data-id');

                    if (confirm('Are you sure you want to delete this record?')) {
                        fetch('delete_history.php', {
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
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', (event) => {
            const studentId = event.target.closest('tr').getAttribute('data-id');
            loadViolationHistory(studentId); // Load violation history for this student
        });
    });
});
</script>

    <!-- Modal Structure -->
    <div id="profileModal" class="modal" >
        <div class="modal-content" style="transform: translate(31%, 25%); position: fixed;">
            <span class="close">&times;</span>
            <div class="profile-container">
                <div class="header">
                    <h1>Student Profile</h1>
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

                <div class="violation-history">
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


<script src="history.js"></script>

<script>
//search-button == enter-key
document.addEventListener('keydown', function(event) {
    // Check if the Enter key is pressed
    if (event.key === 'Enter') {
        // Trigger the "Next" button's click event
        document.getElementById('searchBtn').click();
    }
});
</script>

</body>
</html>
