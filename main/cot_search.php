<?php
// Connect to the database
$servername = "127.0.0.1";
$username = "Kenji";
$password = "JamesRyan";
$dbname = "violation_tracker";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the search query from POST request
$query = isset($_POST['query']) ? $_POST['query'] : '';

if (!empty($query)) {
    // Sanitize the search query
    $query = $conn->real_escape_string($query);

    // Search by student ID or last name
    $sql = "SELECT * FROM violations WHERE student_id LIKE '%$query%' OR lastname LIKE '%$query%'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $records = array();

        // Fetch the results
        while ($row = $result->fetch_assoc()) {
            $records[] = array(
                'student_id' => $row['student_id'],
                'lastname' => $row['lastname'],
                'firstname' => $row['firstname'],
                'violation' => $row['violation'],
                'timestamp' => $row['timestamp'],
                'image_url' => !empty($row['image_url']) ? $row['image_url'] : 'ID/Students/default.jpg',
            );
        }

        // Return the records in JSON format
        echo json_encode(array('success' => true, 'records' => $records));
    } else {
        echo json_encode(array('success' => false));
    }
} else {
    echo json_encode(array('success' => false, 'message' => 'Empty query.'));
}

$conn->close();
?>
