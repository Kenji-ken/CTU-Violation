document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById('search');
    const searchBtn = document.getElementById('searchBtn');
    const tableBody = document.getElementById('student-table-body');



    searchBtn.addEventListener('click', function () {
        const query = searchInput.value.trim();
        console.log(query)
        if (query === "") {
            alert('Please enter a search term.');
            return;
        }

        // Send the search query via AJAX
        fetch('search.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'query=' + encodeURIComponent(query),
        })
            .then(response => response.json())
            .then(data => {
                console.log("data", data);
                if (data.success) {
                    // Clear the current table rows
                    tableBody.innerHTML = '';

                    // Append new rows
                    // Inside your fetch success callback, right before calling reattachEventListeners
                    data.records.forEach(row => {
                        console.log(row);
                        const newRow = document.createElement('tr');
                        newRow.setAttribute('data-id', row.student_id); // Add this line
                        newRow.innerHTML = `
                        <td>${row.student_id}</td>
                        <td class='lastname'>${row.lastname}</td> <!-- Add class here -->
                        <td class='firstname'>${row.firstname}</td> <!-- Add class here -->
                        <td class='violation'>${row.violation}</td> <!-- Add class here -->
                        <td class='offense'>1st Offense</td> <!-- Add class here -->
                        <td class='sanction'>Warning</td> <!-- Add class here -->
                        <td class='timestamp'>${row.timestamp}</td> <!-- Add class here -->
                        <td><img class='profile-image' src='${row.image_url}' alt='Profile Image' width='50' /></td>
                        <td>
                            <button class='edit-btn' data-id='${row.student_id}'>Edit</button>
                            <button class='delete-btn' data-id='${row.student_id}'>Delete</button>
                        </td>
                        <td class='timer' data-time='${row.end_time}' data-cycle='0'>${formatTime(259200)}</td>
                        `;
                        tableBody.appendChild(newRow);

                        // Log the new row for debugging
                        console.log('New Row Added:', newRow);
                    });

                    // Re-attach event listeners after adding the new rows
                    reattachEventListeners(); // Ensure this includes the edit button listener
                    startTimers(); // Call to start timers after new rows are added
                } else {
                    alert('No records found.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });

    // Function to reattach event listeners for search results
    function reattachEventListeners() {
        document.querySelectorAll('.edit-btn').forEach(button => {
            const newButton = button.cloneNode(true); // Create a clone of the button
            button.replaceWith(newButton); // Replace old button with the new button
        });
        // Re-attach edit button functionality
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', (event) => {
                const row = event.target.closest('tr');
                const studentId = row.getAttribute('data-id');

                // Populate modal with student data
                populateModalWithRowData(row, studentId);
                openModal();
            });
        });
        // Re-attach delete button functionality
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
                                location.reload();
                            } else {
                                alert('Error deleting record: ' + data.error);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while deleting the record.');
                        });
                }
            });
        });
    }


    function populateModalWithRowData(row, studentId) {
        console.log('Student ID:', studentId); // Should log the correct student ID

        document.getElementById('student-id').textContent = studentId; // This line should set the ID

        const firstnameElement = row.querySelector('.firstname');
        const lastnameElement = row.querySelector('.lastname');
        const violationElement = row.querySelector('.violation');
        const offenseElement = row.querySelector('.offense');
        const sanctionElement = row.querySelector('.sanction');
        const timestampElement = row.querySelector('.timestamp');
        const imageElement = row.querySelector('.profile-image');

        if (firstnameElement && lastnameElement && violationElement && offenseElement && sanctionElement && timestampElement && imageElement) {
            const studentName = `${firstnameElement.textContent} ${lastnameElement.textContent}`;
            const violation = violationElement.textContent;
            const offense = offenseElement.textContent;
            const sanction = sanctionElement.textContent;
            const timestamp = timestampElement.textContent;
            const imageUrl = imageElement.src;

            // Log the extracted values for debugging
            console.log('Extracted Values:', {
                studentId,
                studentName,
                violation,
                offense,
                sanction,
                timestamp,
                imageUrl,
            });

            // Populate modal with student data
            document.getElementById('student-id').textContent = studentId;
            document.getElementById('student-name').textContent = studentName;
            document.getElementById('violation').value = violation;
            document.getElementById('offense').value = offense;
            document.getElementById('sanction').value = sanction;
            document.getElementById('time').value = timestamp;
            document.getElementById('student-image').src = imageUrl;
        } else {
            console.error('One or more elements were not found in the row:', row);
        }
    }


    // Function to open the modal
    function openModal() {
        const modal = document.getElementById('profileModal');
        modal.style.display = 'flex'; // Show the modal
    }

    // Function to close the modal
    function closeModal() {
        const modal = document.getElementById('profileModal');
        modal.style.display = 'none'; // Hide the modal
    }

    // Initial page edit button functionality
    function attachInitialEditListeners() {
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', (event) => {
                const row = event.target.closest('tr');
                const studentId = row.getAttribute('data-id');

                console.log('Edit Button Clicked. Student ID:', studentId);

                // Get student data from the row
                populateModalWithRowData(row, studentId);
                openModal();
            });
        });
    }

    // Call to attach initial edit button listeners
    attachInitialEditListeners();
    // Function to format seconds into days, hours, minutes, and seconds
    function formatTime(seconds) {
        const days = Math.floor(seconds / 86400); // 86400 seconds in a day
        const hours = Math.floor((seconds % 86400) / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = Math.floor(seconds % 60);
        return `${days}d:${hours < 10 ? '0' : ''}${hours}:${minutes < 10 ? '0' : ''}${minutes}:${secs < 10 ? '0' : ''}${secs}`;
    }

    // Function to start the countdown for each timer
    function startTimers() {
        const rows = tableBody.querySelectorAll('tr');

        rows.forEach(row => {
            const timerCell = row.querySelector('.timer');
            const studentId = row.getAttribute("data-id");
            const violationId = row.querySelector(".timer").getAttribute("data-id");

            // Set an interval to count down
            const intervalId = setInterval(async () => {

                let endTime = new Date(timerCell.getAttribute('data-time'));
                const dateNow = new Date();
    
    
                let timeLeft = (endTime - dateNow) / 1000;  // Get the time in seconds (initially 259200 for 3 days)
                let cycle = parseInt(timerCell.getAttribute('data-cycle'), 10);


                timeLeft--;
                if (timeLeft >= 0) {
                    timerCell.textContent = formatTime(timeLeft);  // Display formatted time
                } else {
                    // Timer finished, update the violation, offense, and sanction

                    // Reset timer to 3 days (259,200 seconds) and increment cycle
                    let date = new Date(timerCell.getAttribute('data-time'));
                    // timeLeft = date.getDate() + 3; // Reset to 3 days

                    date.setDate(date.getDate() + 3);
                    cycle++;

                    updateViolation(studentId, violationId,  row, cycle);

                    timerCell.setAttribute('data-time', date.toISOString().slice(0, 19).replace("T", " "));
                    timerCell.setAttribute('data-cycle', cycle);

                    timerCell.textContent = formatTime(0);
                }

            }, 1000);  // Timer decreases every second
        });
    }

    // Function to update Violation, Offense, and Sanction based on cycle count
    async function updateViolation(studentId, violationId, row, cycle) {
        const violationCell = row.querySelector('.violation');
        const offenseCell = row.querySelector('.offense');
        const sanctionCell = row.querySelector('.sanction');

        // Define the cycle updates
        const updates = [
            { violation: 'Minor Violation', offense: '1st', sanction: 'Warning' },
            { violation: 'Moderate Violation', offense: '2nd', sanction: '1 Day Suspension' },
            { violation: 'Major Violation', offense: '3rd', sanction: '1 Week Suspension' }
        ];

        // Get the appropriate update or loop back to first
        const currentUpdate = updates[cycle % updates.length];

        const formData = new FormData();

        formData.append("student_id", studentId)
        formData.append("violation_id", violationId)
        formData.append("violation", currentUpdate.violation);
        formData.append("offense", currentUpdate.offense);
        formData.append("sanction", currentUpdate.sanction);
        formData.append("cycle", cycle);

        const res = await fetch("update_time.php", {
            method: "POST",
            body: formData
        });
        const data = await res.json();


        console.log(data);

        // Update the table cells
        violationCell.textContent = currentUpdate.violation;
        offenseCell.textContent = currentUpdate.offense;
        sanctionCell.textContent = currentUpdate.sanction;
    }

    // Start all timers on page load
    startTimers();
});
document.addEventListener("DOMContentLoaded", () => {
    // Previous code...

    // Print Button functionality
    document.querySelector('.print').addEventListener('click', () => {
        window.print(); // Print the current page
    });

    // Cancel Button functionality
    document.querySelector('.cancel').addEventListener('click', closeModal); // Close the modal
});
document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', (event) => {
        const row = event.target.closest('tr');
        const studentId = row.getAttribute('data-id');

        // Get student data from the row
        const studentName = row.querySelector('.firstname').textContent + ' ' + row.querySelector('.lastname').textContent;
        const violation = row.querySelector('.violation').textContent; // Get exact violation
        const offense = row.querySelector('.offense').textContent; // Get offense text
        const sanction = row.querySelector('.sanction').textContent; // Get sanction text
        const timestamp = row.querySelector('.timestamp').textContent; // Get the specific timestamp format
        const imageUrl = row.querySelector('.profile-image').src; // Get image URL

        // Populate modal with student data
        document.getElementById('student-id').textContent = studentId;
        document.getElementById('student-name').textContent = studentName;
        document.getElementById('violation').value = violation; // Set exact violation text here
        document.getElementById('offense').value = offense; // Show offense
        document.getElementById('sanction').value = sanction; // Show sanction
        document.getElementById('time').value = timestamp; // Show specific timestamp

        // Set the image in the modal
        document.getElementById('student-image').src = imageUrl; // Update image in modal

        // Open the modal
        openModal();
    });
});
// Print functionality
document.querySelector('.print').addEventListener('click', () => {
    window.print(); // Trigger the print dialog
});

// Cancel functionality
document.querySelector('.cancel').addEventListener('click', () => {
    closeModal(); // Assuming you have a closeModal function to hide the modal
});
function closeModal() {
    const modal = document.getElementById('yourModalId'); // Change this to your modal's ID
    modal.style.display = 'none'; // Hide the modal
}
// Function to close the modal
function closeModal() {
    const modal = document.getElementById('profileModal'); // Ensure this matches your modal ID
    modal.style.display = 'none'; // Hide the modal
}

// Event listener for the Cancel button
document.addEventListener("DOMContentLoaded", () => {
    // Other code...

    // Cancel Button functionality
    document.querySelector('.cancel').addEventListener('click', closeModal); // Close the modal when clicked
});
// Fetch violation history
fetch('get_violation_history.php?student_id=' + encodeURIComponent(studentId))
    .then(response => response.json())
    .then(data => {
        const historySection = document.getElementById('violation-history');
        if (data.success) {
            // Display the history
            if (data.history.length > 0) {
                historySection.innerHTML = data.history.map(violation => `
                    <div>
                        <p><strong>Date and Time:</strong> ${new Date(violation.timestamp).toLocaleString()}</p>
                        <p><strong>Violation:</strong> ${violation.violation}</p>
                        <hr>
                    </div>
                `).join('');
            } else {
                historySection.innerHTML = 'No Records - ' + studentName;
            }
        } else {
            historySection.innerHTML = 'No Records - ' + studentName;
        }
    })
    .catch(error => {
        console.error('Error fetching violation history:', error);
    });
document.addEventListener('DOMContentLoaded', () => {
    // Open the staff modal when the dashboard link is clicked
    const dashboardLink = document.getElementById('dashboardLink');
    const staffModal = document.getElementById('staffModal');

    dashboardLink.addEventListener('click', (e) => {
        e.preventDefault(); // Prevent the default link behavior
        staffModal.style.display = 'block'; // Show the staff modal
    });

    // Close staff modal when clicking the close button
    document.getElementById('closeStaff').onclick = function () {
        staffModal.style.display = 'none';
    };

    // Close modal when clicking outside of it
    window.onclick = function (event) {
        if (event.target == staffModal) {
            staffModal.style.display = 'none';
        }
    };
});
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


function handleSortDropdown() {
    const dropdown = document.getElementById('sortDropdown');
    const selectedOption = dropdown.value;

    if (!selectedOption) return; // Do nothing if no option is selected

    const [columnIndex, type] = selectedOption.split('-');
    sortTable(parseInt(columnIndex), type);
}

function sortTable(columnIndex, type) {
    const table = document.querySelector('table');
    const rows = Array.from(table.querySelectorAll('tbody tr'));

    rows.sort((a, b) => {
        let aData = a.cells[columnIndex].textContent.trim();
        let bData = b.cells[columnIndex].textContent.trim();

        // Handle different column types
        if (type === 'number') {
            aData = parseInt(aData, 10) || 0; // Parse as an integer, default to 0 if NaN
            bData = parseInt(bData, 10) || 0;

            // Compare for ascending order
            if (aData > bData) return 1;
            if (aData < bData) return -1;
            return 0;
        } else if (type === 'date') {
            aData = new Date(aData).getTime();
            bData = new Date(bData).getTime();

            // Sort dates in descending order (most recent first)
            if (aData < bData) return 1;
            if (aData > bData) return -1;
            return 0;
        } else if (type === 'string') {
            aData = aData.toLowerCase();
            bData = bData.toLowerCase();

            // Compare strings for ascending order
            if (aData > bData) return 1;
            if (aData < bData) return -1;
            return 0;
        }
    });

    // Append sorted rows back to the table body
    const tbody = table.querySelector('tbody');
    tbody.innerHTML = '';
    rows.forEach(row => tbody.appendChild(row));
}
