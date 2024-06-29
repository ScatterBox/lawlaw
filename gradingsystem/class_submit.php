<?php
// Include database connection file
include('conn.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the submitted form data
    $subject = $_POST['subject'];
    $yearLevel = $_POST['yearLevel'];
    $section = $_POST['section'];

    // Get the ID of the logged-in teacher
    $teacherId = $_SESSION['user']['user_id'];

    // Insert the new class into the classes table
    $sql = "INSERT INTO classes (user_id, sub_id, section) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param('iss', $teacherId, $subject, $section);
    $stmt->execute();

    // Get the ID of the newly inserted class
    $classId = $conn->insert_id;

    // Select all students with the selected year level and section
    $sql = "SELECT user_id FROM students WHERE year_level = ? AND section = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param('ss', $yearLevel, $section);
    $stmt->execute();
    $result = $stmt->get_result();

    // For each student, update their class_id to the new class ID
    $sql = "UPDATE students SET class_id = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    while ($row = $result->fetch_assoc()) {
        $stmt->bind_param('ii', $classId, $row['user_id']);
        $stmt->execute();
    }

    echo "Class created successfully!";
}
?>
