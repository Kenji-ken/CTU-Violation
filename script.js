// Admin modal functionality (unchanged)
const adminBtn = document.getElementById('admin-btn');
const adminModal = document.getElementById('adminModal');
const closeAdmin = document.getElementById('closeAdmin');

adminBtn.addEventListener('click', function () {
    adminModal.style.display = 'flex';
});

closeAdmin.addEventListener('click', function () {
    adminModal.style.display = 'none';
});

// Staff modal functionality (unchanged)
const staffBtn = document.getElementById('staff-btn');
const staffModal = document.getElementById('staffModal');
const closeStaff = document.getElementById('closeStaff');

staffBtn.addEventListener('click', function () {
    staffModal.style.display = 'flex';
});

closeStaff.addEventListener('click', function () {
    staffModal.style.display = 'none';
});

// Adding student search functionality (no redirection)
document.getElementById('search-student-form').addEventListener('submit', function (event) {
    event.preventDefault();

    const studentId = document.getElementById('student-id').value;

    if (studentId) {
        // Call the function to show the modal with violation data
        showViolationModal(studentId);
    } else {
        alert('Please enter a valid Student ID.');
    }
});

// Function to show the modal with the student's violation record
function showViolationModal(studentId) {
    // Fetch student violation data via AJAX
    fetch(`fetch_violation.php?student_id=${encodeURIComponent(studentId)}`)
        .then(response => response.json())
        .then(data => {
            const modal = document.getElementById('violationModal');
            const violationDetails = document.getElementById('violation-details');

            if (data.success) {
                const student = data.student;
                console.log("student", student);
                violationDetails.innerHTML = `
                    <h2>Student Profile</h2>
                    <img src="${student.photo}" alt="Student Photo" style="border-radius: 50%; width: 150px; height: 150px;">
                    <p><strong>ID Number:</strong> ${student.student_id}</p>
                    <p><strong>Name:</strong> ${student.firstname} ${student.lastname}</p>
                    <p><strong>Course:</strong> ${student.course}</p>
                    <p><strong>Department:</strong> ${student.department}</p>
                    <table>
                        <tr>
                            <th>Violation</th>
                            <th>Offense</th>
                            <th>Sanction</th>
                            <th>Time</th>
                        </tr>
                        <tr>
                            <td>${student.violation}</td>
                            <td>${student.offense}</td>
                            <td>${student.sanction}</td>
                            <td>${student.time}</td>
                        </tr>
                    </table>
                `;
            } else {
                violationDetails.innerHTML = `
                    <h2>No Violation Records</h2>
                    <p>No records found for student ID: ${studentId}</p>
                `;
            }

            modal.style.display = 'flex';
        })
        .catch(error => {
            console.error('Error fetching violation data:', error);
        });
}

// Close the violation modal
const closeViolationModal = document.getElementById('closeViolationModal');
closeViolationModal.addEventListener('click', function () {
    document.getElementById('violationModal').style.display = 'none';
});
// Function to show the modal with the student's violation record
function showViolationModal(studentId) {
    // Fetch student violation data via AJAX
    fetch(`fetch_violation.php?student_id=${encodeURIComponent(studentId)}`)
        .then(response => response.json())
        .then(data => {
            const modal = document.getElementById('violationModal');
            const violationDetails = document.getElementById('violation-details');

            if (data.success) {
                const student = data.student;

                console.log(student);

                violationDetails.innerHTML = `
                    <h2>Student Profile</h2>
                    <div class="student-info">
                        <img src="${student.photo}" alt="Student Photo">
                     <div class="student-details">
                          <p><strong>ID Number:</strong> ${student.student_id}</p>
                          <p><strong>Name:</strong> ${student.firstname} ${student.lastname}</p>
                          <p><strong>Course:</strong> ${student.course}</p>
                          <p><strong>Department:</strong> ${student.department}</p>
                     </div>
                    </div>

                    <table class="student-violation-record">
                        <tr>
                            <th>Violation</th>
                            <th>Offense</th>
                            <th>Sanction</th>
                            <th class="time-remaining"><img src="images/timer.png">Time Remaining</th> <!-- Updated label -->
                        </tr>
                        <tr>
                            <td>${student.violation}</td>
                            <td>${student.offense}</td>
                            <td>${student.sanction}</td>
                            <td class="time-remaining"><span id="time-remaining" data-cycle="${student.cycle}">${student.time ?? 0}</span></td> <!-- Time element for countdown -->
                        </tr>
                    </table>
                `;



                // Initialize the countdown with the violation time
                const endTime = new Date(student.end_time);
                const dateNow = new Date();
                let remainingTime = (endTime - dateNow) / 1000; // Assuming the time is in seconds

                console.log(remainingTime);
                // let remainingTime = 259200; // Assuming the time is in seconds

                const timeElement = document.getElementById('time-remaining');

                function formatTime(seconds) {
                    let days = Math.floor(seconds / (24 * 60 * 60));
                    let hours = Math.floor((seconds % (24 * 60 * 60)) / (60 * 60));
                    let minutes = Math.floor((seconds % (60 * 60)) / 60);
                    let secs = Math.floor(seconds % 60);

                    return `${days}d ${hours}h ${minutes}m ${secs}s`;
                }

                async function updateTimer() {

                    const updates = [
                        { violation: 'Minor Violation', offense: '1st', sanction: 'Warning' },
                        { violation: 'Moderate Violation', offense: '2nd', sanction: '1 Day Suspension' },
                        { violation: 'Major Violation', offense: '3rd', sanction: '1 Week Suspension' }
                    ];

                    if (remainingTime > 0) {
                        timeElement.textContent = formatTime(remainingTime);
                        remainingTime--;
                    } else {

                        const currentUpdate = updates[student.cycle % updates.length];

                        const formData = new FormData();

                        formData.append("student_id", student.student_id);
                        formData.append("violation_id", student.id);
                        formData.append("violation", currentUpdate.violation);
                        formData.append("offense", currentUpdate.offense);
                        formData.append("sanction", currentUpdate.sanction);
                        formData.append("cycle", student.cycle++);

                        const res = await fetch("main/update_time.php", {
                            method: "POST",
                            body: formData
                        });
                        const data = await res.json();

                        clearInterval(timerInterval);
                        timeElement.textContent = "Time's up!";
                    }
                }

                // Start the countdown timer
                const timerInterval = setInterval(updateTimer, 1000);

            } else {
                violationDetails.innerHTML = `
                    <h2>No Violation Records</h2>
                    <p>No records found for student ID: ${studentId}</p>
                `;
            }

            modal.style.display = 'flex';
        })
        .catch(error => {
            console.error('Error fetching violation data:', error);
        });

}
