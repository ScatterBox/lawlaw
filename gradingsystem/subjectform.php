<?php
header('Content-Type: application/json');
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gradingsystem";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject_name = $_POST["subject_name"];
    $year_level = $_POST["year_level"];
    $section = $_POST["section"];
    $created_by = $_SESSION["user"]["user_id"]; // Assuming user_id is stored in session

    // Check if the same subject exists in the same year and section
    $checkSql = "SELECT * FROM subjects WHERE subject_name = ? AND year_level = ? AND section = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("sss", $subject_name, $year_level, $section);
    $stmt->execute();
    $checkResult = $stmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Error: The same subject exists in the same year and section']);
        exit();
    }

    $stmt->close();

    // Insert the subject into the subjects table
    $sql = "INSERT INTO subjects (subject_name, year_level, section, created_by) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $subject_name, $year_level, $section, $created_by);

    if ($stmt->execute()) {
        $subject_id = $stmt->insert_id;

        // Assign the subject to students in the same year and section
        $assignSql = "INSERT INTO student_subjects (student_id, subject_id) 
                      SELECT user_id, ? 
                      FROM students 
                      WHERE year_level = ? AND section = ?";
        $assignStmt = $conn->prepare($assignSql);
        $assignStmt->bind_param("iss", $subject_id, $year_level, $section);
        $assignStmt->execute();
        $assignStmt->close();

        echo json_encode(['success' => true, 'message' => 'New subject created successfully and assigned to students']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
