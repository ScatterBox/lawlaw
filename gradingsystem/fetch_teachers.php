<?php
include 'conn.php'; // Include your database connection file

$sql = "SELECT t.user_id, t.fname, t.mname, t.lname, t.ename, t.age, t.gender, t.address, 
               GROUP_CONCAT(s.subject_name SEPARATOR ', ') AS subjects 
        FROM teachers t
        LEFT JOIN subjects s ON t.user_id = s.created_by
        GROUP BY t.user_id";

$result = $conn->query($sql);

$data = array();
while($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Output to JSON format
header('Content-Type: application/json');
echo json_encode($data);

$conn->close();
?>
