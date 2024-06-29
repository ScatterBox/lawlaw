<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gradingsystem";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

$sql = "SELECT s.user_id, s.fname, s.mname, s.lname, s.age, s.gender, s.birthdate, s.address, 
               s.year_level, s.section, s.email, s.lrn, 
               GROUP_CONCAT(sb.subject_name SEPARATOR ', ') AS subjects 
        FROM students s
        LEFT JOIN student_subjects ss ON s.user_id = ss.student_id
        LEFT JOIN subjects sb ON ss.subject_id = sb.subject_id
        GROUP BY s.user_id";

$result = $conn->query($sql);
$students = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

echo json_encode($students);

$conn->close();
?>
