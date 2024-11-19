<?php
$servername = "127.0.0.1";
$username = "Kenji";
$password = "JamesRyan";
$dbname = "violation_tracker";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['rfid'])) {
        // Search by RFID
        $rfid_id = $_POST['rfid'];
        
        $sql = "SELECT * FROM students WHERE rfid_number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $rfid_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Log successful RFID scan
            logRFIDScan($conn, $rfid_id, $row['student_id'], true);
            echo json_encode($row);
        } else {
            // Log failed RFID scan
            logRFIDScan($conn, $rfid_id, null, false);
            echo json_encode(['error' => 'No student found with this RFID card']);
        }
    }
    elseif (!empty($_POST['search-id'])) {
        $search_id = $_POST['search-id'];

        $sql = "SELECT * FROM students WHERE student_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $search_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode($row);
        } else {
            echo json_encode(['error' => 'No student found']);
        }
    } 
    elseif (!empty($_POST['search-lastname'])) {
        $search_lastname = $_POST['search-lastname'];

        $sql = "SELECT * FROM students WHERE lastname = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $search_lastname);
        $stmt->execute();
        $result = $stmt->get_result();

        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }

        if (!empty($students)) {
            echo json_encode($students);
        } else {
            echo json_encode(['error' => 'No student found']);
        }
    }
}

// Function to log RFID scans
function logRFIDScan($conn, $rfid_id, $student_id, $success) {
    $sql = "INSERT INTO rfid_logs (rfid_number, student_id, scan_time, success) VALUES (?, ?, NOW(), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $rfid_id, $student_id, $success);
    $stmt->execute();
}

// Close the database connection
$conn->close();
?>