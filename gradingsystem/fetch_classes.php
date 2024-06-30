<?php
session_start();
if ($_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}

$created_by = $_SESSION['user']['user_id'];

$conn = new mysqli('localhost', 'root', '', 'gradingsystem');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM subjects WHERE created_by = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $created_by);
$stmt->execute();
$result = $stmt->get_result();

$classes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
}

$stmt->close();
$conn->close();

echo json_encode($classes);
?>
