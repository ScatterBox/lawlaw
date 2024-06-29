<?php
// Include database connection file
include('conn.php');

// Check if the grade level is set
if (isset($_POST['grade'])) {
    $gradeLevel = $_POST['grade'];

    // SQL query to fetch data from the students table based on the year_level
    $sql = "SELECT * FROM students WHERE year_level = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $gradeLevel);
    $stmt->execute();
    $result = $stmt->get_result();

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
                'section' => $row['section']
            ];
        }
    }

    // Output the data in JSON format
    echo json_encode($students);
}
?>
