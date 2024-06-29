<?php
include 'conn.php'; // Include your database connection file

$sql = "SELECT user_id, fname, mname, lname, ename, 
               age, 
               address, 
               gender
        FROM teachers"; // Adjust your SQL query accordingly
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
