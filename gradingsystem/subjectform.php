<?php
header('Content-Type: application/json');
session_start(); // Start session to access user information

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gradingsystem";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject_name = $_POST["subject_name"];
    $year_level = $_POST["year_level"];
    $section = $_POST["section"];
    $created_by = $_SESSION["user_id"]; // Assuming user_id is stored in session (teacher's ID)

    // Check if the same subject exists in the same year and section
    $checkSql = "SELECT * FROM class WHERE subject_name = '$subject_name' AND year_level = '$year_level' AND section = '$section'";
    $checkResult = $conn->query($checkSql);
    if ($checkResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Error: The same subject exists in the same year and section']);
        exit();
    }

    $sql = "INSERT INTO class (subject_name, year_level, section, created_by)
            VALUES ('$subject_name', '$year_level', '$section', '$created_by')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'New record created successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $sql . '<br>' . $conn->error]);
    }
}

$conn->close();
?>
