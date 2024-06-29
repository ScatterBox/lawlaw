<?php
header('Content-Type: application/json');
include 'conn.php';

// Fetch student data with corresponding subjects
$sql = "
SELECT students.*, class.subject_name
FROM students
LEFT JOIN class ON students.year_level = class.year_level AND students.section = class.section";
$result = $conn->query($sql);

$students = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

echo json_encode($students);

$conn->close();
?>
