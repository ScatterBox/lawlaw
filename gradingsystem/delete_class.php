<?php
session_start();
if ($_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}

$subject_id = $_POST['subject_id'];

$conn = new mysqli('localhost', 'root', '', 'gradingsystem');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "DELETE FROM subjects WHERE subject_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $subject_id);
$success = $stmt->execute();
$stmt->close();
$conn->close();

$response = ['success' => $success];
echo json_encode($response);
?>
