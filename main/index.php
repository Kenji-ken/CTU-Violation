<?php
// index.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Violation Tracker</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to external CSS file -->
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<div class="ctu-background-modify">
    <div class="container">
        <div class="left-container">
            <img src="images/created.png" alt="CTU Logo" class="logo">
            <div class="search-forms">
                <label for="search-id">Search ID Number</label>
                <input type="text" placeholder="Search ID Number" id="search-id">
                <div class="separator"><div class="line1"></div> or <div class="line2"></div></div>
                <label for="search-lastname">Search Lastname</label>
                <input type="text" placeholder="Search Lastname" id="search-lastname">
                <!-- "Next" Button -->
                <button id="next-button">Next</button>
            </div>
            <div class="ctu-background">
	       <img src="images/ctu-background.png">
            </div>
        </div>

        <!-- Right content container -->
        <div class="main-content">
            <div class="right-container">
                <img src="images/created.png" alt="CTU Logo" class="right-logo">
                <h1 class="logo-text">Violation Tracker </h1>
                <div class="student-profile">
                    <img id="profile-image" src="images/profile-icon.png" > <!-- Adjusted margin-top -->
                    <div class="vertical-line"></div>
                    <div class="text-beside-line">
                       <p>ID Number: <span id="display-id"></span></p>
                       <p>Lastname: <span id="display-lastname"></span></p>
                       <p>Firstname: <span id="display-firstname"></span></p>
                       <p>Course: <span id="display-course"></span></p>
                       <p>Department: <span id="display-department"></span></p>
                    </div>
                </div>              
                <div class="violation-text">
                    Violation:
                    <select id="light-offense" onchange="toggleOthersInput()">
                        <option value="wearing-colored-shirts">Colored Shirt</option>
                        <option value="multiple-earings">Multiple Earrings</option>
                        <option value="no-haircut">No haircut</option>
                        <option value="others">Others</option> <!-- Added "Others" option -->
                    </select> 
                </div>

                <!-- Input for specifying other violations -->
                <div id="others-input-container"> <!-- Adjusted margin-top -->
                    <label for="others-violation">Specify Violation:</label>
                    <input type="text" id="others-violation" placeholder="Specify the violation">
                </div>

                <div class="offense-text">
                    Offense:
                    <select id="offense">
                        <option value="1st">1st Offense</option>
                        <option value="2nd">2nd Offense</option>
                        <option value="3rd">3rd Offense</option>
                        <option value="severe">Severe Offense</option>
                    </select> 
                </div>

                <div class="sanction-text">
                    Sanction:
                    <select id="sanction">
                        <option value="warning">Warning</option>
                        <option value="detention">Detention</option>
                        <option value="suspension">Suspension</option>
                    </select> 
                </div>

                <div class="right-buttons">
                    <button class="cancel-button">Cancel</button>
                    <button class="submit-button">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- Modal for multiple students -->
    <div id="multipleStudentsModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p>Select the correct student:</p>
            <div id="student-list"></div>
        </div>
    </div>

    <!-- Modal for violation report -->
    <div id="violationModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p>Violation report has been submitted.</p>
        </div>
    </div>

    <script>
        document.getElementById('next-button').addEventListener('click', function() {
            var searchID = document.getElementById('search-id').value;
            var searchLastname = document.getElementById('search-lastname').value;

            if (searchID) {
                // Perform search by ID
                fetch('search_student.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'search-id=' + searchID
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        displayStudentInfo(data);
                    }
                });
            } else if (searchLastname) {
                // Perform search by Lastname
                fetch('search_student.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'search-lastname=' + searchLastname
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        if (Array.isArray(data)) {
                            displayMultipleStudents(data);
                        } else {
                            displayStudentInfo(data);
                        }
                    }
                });
            } else {
                alert('Please enter either an ID number or a Lastname.');
            }
        });

        function displayStudentInfo(data) {
            document.getElementById('display-id').textContent = data.student_id;
            document.getElementById('display-lastname').textContent = data.lastname;
            document.getElementById('display-firstname').textContent = data.firstname;
            document.getElementById('display-course').textContent = data.course;
            document.getElementById('display-department').textContent = data.department;
            document.getElementById('profile-image').src = data.profile_image ? data.profile_image : 'images/profile-icon.png';
        }

        function displayMultipleStudents(students) {
            var studentList = document.getElementById('student-list');
            studentList.innerHTML = '';
            students.forEach(student => {
                var studentItem = document.createElement('div');
                studentItem.textContent = `${student.student_id} - ${student.firstname} ${student.lastname}`;
                studentItem.addEventListener('click', function() {
                    displayStudentInfo(student);
                    document.getElementById('multipleStudentsModal').style.display = 'none';
                });
                studentList.appendChild(studentItem);
            });
            document.getElementById('multipleStudentsModal').style.display = 'block';
        }

        // Function to toggle visibility of others input
        function toggleOthersInput() {
            var selectElement = document.getElementById('light-offense');
            var othersInputContainer = document.getElementById('others-input-container');
            
            if (selectElement.value === 'others') {
                othersInputContainer.style.display = 'block'; // Show input box
            } else {
                othersInputContainer.style.display = 'none'; // Hide input box
                document.getElementById('others-violation').value = ''; // Clear input if hiding
            }
        }

        document.querySelector('.submit-button').addEventListener('click', function() {
            var studentID = document.getElementById('display-id').textContent;
            var studentLastname = document.getElementById('display-lastname').textContent;
            var studentFirstname = document.getElementById('display-firstname').textContent;
            var violation = document.getElementById('light-offense').value;
            
            // Get value from the others input if "Others" is selected
            var othersViolation = document.getElementById('others-violation').value;
            if (violation === 'others' && othersViolation.trim() === '') {
                alert('Please specify the violation if "Others" is selected.');
                return;
            }

            var offense = document.getElementById('offense').value;
            var sanction = document.getElementById('sanction').value;
            var studentCourse = document.getElementById('display-course').textContent;
            var studentDepartment = document.getElementById('display-department').textContent;

            if (!studentID || !violation || !offense || !sanction || !studentCourse || !studentDepartment) {
                alert('Please fill all the fields.');
                return;
            }

            // Your original record logic
            console.log({
                studentID,
                studentLastname,
                studentFirstname,
                violation: violation === 'others' ? othersViolation : violation,
                offense,
                sanction,
                studentCourse,
                studentDepartment
            });

            fetch('record_violation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `student_id=${studentID}&lastname=${studentLastname}&firstname=${studentFirstname}&violation=${violation === 'others' ? othersViolation : violation}&offense=${offense}&sanction=${sanction}&course=${studentCourse}&department=${studentDepartment}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Violation recorded successfully');
                    document.getElementById("violationModal").style.display = "block";
                } else {
                    alert('Failed to record violation: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please check the console for details.');
            });
        });

        // Cancel button functionality
        document.querySelector('.cancel-button').addEventListener('click', function() {
            // Clear input fields
            document.getElementById('search-id').value = '';
            document.getElementById('search-lastname').value = '';
            document.getElementById('display-id').textContent = '';
            document.getElementById('display-lastname').textContent = '';
            document.getElementById('display-firstname').textContent = '';
            document.getElementById('display-course').textContent = '';
            document.getElementById('display-department').textContent = '';
            document.getElementById('profile-image').src = ''; // Clear profile image

            // Hide the others input
            document.getElementById('others-input-container').style.display = 'none';
            document.getElementById('others-violation').value = ''; // Clear others violation input

            // Hide the modals
            document.getElementById("multipleStudentsModal").style.display = "none";
            document.getElementById("violationModal").style.display = "none";
        });

        // Modal close functionality
        document.querySelectorAll('.close').forEach(element => {
            element.addEventListener('click', function() {
                this.parentElement.parentElement.style.display = 'none';
            });
        });

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        };
    </script>
</body>
</html>