<?php
session_start();
if ($_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}

$year_level = $_POST['year_level'];
$section = $_POST['section'];

$conn = new mysqli('localhost', 'root', '', 'gradingsystem');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM students WHERE year_level = ? AND section = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $year_level, $section);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

$stmt->close();
$conn->close();

echo json_encode($students);
?>
