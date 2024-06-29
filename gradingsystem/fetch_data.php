<?php
// Include database connection file
include('conn.php');

// SQL query to fetch data from the students table for the specified sections
$sql = "SELECT * FROM students WHERE section IN ('section1', 'section2', 'section3', 'section4', 'section5', 'section6', 'section7')";
$result = $conn->query($sql);

$students = [];
if ($result->num_rows > 0) {
    // Fetch each row as an associative array
    while ($row = $result->fetch_assoc()) {
        $students[] = [
            'fullname' => $row['fname'] . ' ' . $row['mname'] . ' ' . $row['lname'] . ' ' . $row['ename'],
            'age' => $row['age'],
            'gender' => $row['gender'],
            'birthdate' => $row['birthdate'],
            'address' => $row['address'],
            'section' => $row['section'],
            'email' => $row['email'],
            'lrn' => $row['lrn'],
            'year_level' => $row['year_level']
        ];
    }
}

// Output the data in JSON format
echo json_encode($students);
?>
