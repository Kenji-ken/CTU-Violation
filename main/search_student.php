<?php
$servername = "127.0.0.1";
$username = "Kenji";
$password = "JamesRyan"; // your database password
$dbname = "violation_tracker";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['search-id'])) {
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
    } elseif (!empty($_POST['search-lastname'])) {
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
?>
