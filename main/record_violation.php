<?php
// Connect to the database
$servername = "127.0.0.1";
$username = "Kenji";  // Replace with your DB username
$password = "JamesRyan";  // Replace with your DB password
$dbname = "violation_tracker";  // Replace with your DB name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect POST data from the form
$student_id = $_POST['student_id'] ?? '';
$lastname = $_POST['lastname'] ?? '';
$firstname = $_POST['firstname'] ?? '';
$violation = $_POST['violation'] ?? '';
$offense = $_POST['offense'] ?? '';
$sanction = $_POST['sanction'] ?? '';
$course = $_POST['course'] ?? '';
$department = $_POST['department'] ?? '';

// Log incoming data
error_log("Received data: " . json_encode($_POST));

// Check if all required fields are present
if (!$student_id || !$lastname || !$firstname || !$violation || !$offense || !$sanction || !$course || !$department) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

// Set default image URL if not provided
$image_url = 'ID/Students/' . $student_id . '.jpg'; // Assuming the student ID corresponds to the image name
if (!file_exists($image_url)) {
    $image_url = 'https://via.placeholder.com/100'; // Default image if specific image not found
}

// Insert violation data into the database
$sql = "INSERT INTO violations (student_id, lastname, firstname, violation, offense, sanction, course, department, image_url, end_time) VALUES ('$student_id', '$lastname', '$firstname', '$violation', '$offense', '$sanction', '$course', '$department', '$image_url', DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 3 DAY))";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$conn->close();