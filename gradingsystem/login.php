<?php include 'styles/hui.php' ?>
<link rel="stylesheet" href="styles/ls.css" />

<?php
// Include database connection file
include ('conn.php');

// Start the session
session_start();

// Initialize error message variable
$errorMsg = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the submitted form data
    $username = $_POST['uname'];
    $password = $_POST['psw'];

    // Function to check user in a specific table
    function check_user($conn, $table, $username, $password) {
        $sql = "SELECT * FROM $table WHERE username = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param('ss', $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Check in admins table
    $user = check_user($conn, 'admins', $username, $password);
    if ($user) {
        $_SESSION['user'] = $user;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = 'admin';
        header("Location: admin.php"); // Redirect to admin dashboard
        exit();
    }

    // Check in students table
    $user = check_user($conn, 'students', $username, $password);
    if ($user) {
        $_SESSION['user'] = $user;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role']; // Use the role from the database
        if ($user['role'] == 'student') {
            header("Location: student.php"); // Redirect to student dashboard
        } else if ($user['role'] == 'teacher') {
            header("Location: teacher.php"); // Redirect to teacher dashboard
        }
        exit();
    }

    // Check in teachers table
    $user = check_user($conn, 'teachers', $username, $password);
    if ($user) {
        $_SESSION['user'] = $user;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = 'teacher';
        header("Location: teacher.php"); // Redirect to teacher dashboard
        exit();
    }

    // If no match found
    $errorMsg = "Invalid username or password.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>

    <div id="login" class="login d-flex align-items-center justify-content-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 d-flex align-items-center justify-content-center">
                    <form action="login.php" method="post" class="form_container">
                        <div class="shesh mb-4">
                            <img src="/gradingsystem/images/logo.jpg" alt="">
                            <p class="h2 fw-bold">FGSNHS</p>
                        </div>
                        <div class="form-group mb-2">
                            <label for="uname"><b>Username</b></label>
                            <input type="text" class="form-control" placeholder="Enter Username" name="uname" required>
                        </div>
                        <div class="form-group mb-2">
                            <label for="psw"><b>Password</b></label>
                            <input type="password" class="form-control" placeholder="Enter Password" name="psw"
                                required>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Check if there's an error message and display it
        var errorMsg = "<?php echo $errorMsg; ?>";
        if (errorMsg) {
            alert(errorMsg);
            document.getElementById('uname').value = '';
            document.getElementById('psw').value = '';
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>
</body>
</html>
