<?php
// tdbconn.php

include 'conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = $_POST["fname"];
    $mname = $_POST["mname"];
    $lname = $_POST["lname"];
    $ename = $_POST["ename"];
    $nickname = $_POST["nickname"];
    $age = $_POST["age"];
    $gender = $_POST["gender"];
    $birthdate = $_POST["birthdate"];
    $address = $_POST["address"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $role = $_POST["role"];
    $img = $_POST["img"];

    // Check if a record with the same username already exists
    $checkSql = "SELECT * FROM teachers WHERE username='$username'";
    $checkResult = $conn->query($checkSql);

    if ($checkResult->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Error: A user with the same username already exists.']);
    } else {
        // Check if a record with the same fname, mname, lname, and ename already exists
        $checkSql = "SELECT * FROM teachers WHERE fname='$fname' AND mname='$mname' AND lname='$lname'";
        if ($ename) {
            $checkSql .= " AND ename='$ename'";
        }
        $checkResult = $conn->query($checkSql);

        if ($checkResult->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Error: A user with the same full name already exists.']);
        } else {
            // No such record exists, you can proceed with the INSERT operation
            $sql = "INSERT INTO teachers (fname, mname, lname, ename, nickname, age, gender, birthdate, address, username, password, role, img)
            VALUES ('$fname', '$mname', '$lname', '$ename', '$nickname', '$age', '$gender', '$birthdate', '$address', '$username', '$password', '$role', '$img')";

            if ($conn->query($sql) === TRUE) {
                echo json_encode(['success' => true, 'message' => 'New record created successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $sql . '<br>' . $conn->error]);
            }
        }
    }

    $conn->close();
}
?>
