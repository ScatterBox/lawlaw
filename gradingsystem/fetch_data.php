<?php
// Include database connection file
include('conn.php');

// SQL query to fetch data from the students table
$sql = "SELECT s.user_id, s.fname, s.mname, s.lname, s.ename, s.age, s.gender, s.birthdate, s.address, 
               s.year_level, s.section, s.email, s.lrn, 
               GROUP_CONCAT(sb.subject_name SEPARATOR ', ') AS subjects 
        FROM students s
        LEFT JOIN student_subjects ss ON s.user_id = ss.student_id
        LEFT JOIN subjects sb ON ss.subject_id = sb.subject_id AND sb.year_level = s.year_level AND sb.section = s.section
        GROUP BY s.user_id";

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
            'year_level' => $row['year_level'],
            'section' => $row['section'],
            'subjects' => $row['subjects'],
            'email' => $row['email'],
            'lrn' => $row['lrn']
        ];
    }
}

// Output the data in JSON format
echo json_encode($students);

$conn->close();
?>
