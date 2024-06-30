<?php
session_start();
if ($_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}

function getNewClassFormHtml()
{
    $created_by = $_SESSION['user']['fname'] . ' ' . $_SESSION['user']['mname'] . ' ' . $_SESSION['user']['lname'] . ' ' . $_SESSION['user']['ename']; // Fetch the full name from the session data

    // Fetch distinct year levels and sections from the database
    $conn = new mysqli('localhost', 'root', '', 'gradingsystem');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch distinct year levels
    $yearLevelsQuery = "SELECT DISTINCT year_level FROM students";
    $yearLevelsResult = $conn->query($yearLevelsQuery);

    // Fetch distinct sections
    $sectionsQuery = "SELECT DISTINCT section FROM students";
    $sectionsResult = $conn->query($sectionsQuery);

    $yearLevels = [];
    $sections = [];

    if ($yearLevelsResult->num_rows > 0) {
        while ($row = $yearLevelsResult->fetch_assoc()) {
            $yearLevels[] = $row['year_level'];
        }
    }

    if ($sectionsResult->num_rows > 0) {
        while ($row = $sectionsResult->fetch_assoc()) {
            $sections[] = $row['section'];
        }
    }

    $conn->close();

    // Generate HTML for the form with dynamic options
    $html = '
        <style>
            h3 { margin-top: 20px; }
        </style>
        <h3>Class Create Form</h3>
        <div class="registration-form">
            <form id="adminForm">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="subject_name">Subject Name:</label>
                            <input type="text" class="form-control item" id="subject_name" name="subject_name" placeholder="Example: Math" required>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="year_level">Year Level:</label>
                            <select class="form-control item" id="year_level" name="year_level" required>
                                <option value="">Select Year Level</option>';
    foreach ($yearLevels as $level) {
        $html .= '<option value="' . $level . '">' . $level . '</option>';
    }
    $html .= '
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="section">Section assigned:</label>
                            <select class="form-control item" id="section" name="section" required>
                                <option value="">Select Section</option>';
    foreach ($sections as $section) {
        $html .= '<option value="' . $section . '">' . $section . '</option>';
    }
    $html .= '
                            </select>
                        </div>
                    </div>
                    <input type="hidden" id="created_by" name="created_by" value="' . $created_by . '">
                    <div class="col-md-8">
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Create Class</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    ';

    return $html;
}

$newClassFormHtml = getNewClassFormHtml();
?>

<?php include 'styles/hui.php' ?>
<link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
<link rel="stylesheet" href="styles/style.css">

<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.0.8/datatables.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />


<body>
    <div class="container-fluid">
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="images/logo.jpg" alt="logo" />
                <div class="header-text">
                    <h2>Teacher</h2>
                    <h2>Dashboard</h2>
                </div>
            </div>
            <ul class="sidebar-links">
                <h4>
                    <span>Manage Info</span>
                    <div class="menu-separator"></div>
                </h4>
                <li>
                    <a href="#" onclick="showManageClass()">
                        <span class="material-symbols-outlined">
                            person
                        </span>
                        View Students</a>
                </li>
                <li>
                    <a href="#" onclick="showClasses()">
                        <span class="material-symbols-outlined">
                            person_add
                        </span>View Class</a>
                </li>

                <h4>
                    <span>Account</span>
                    <div class="menu-separator"></div>
                </h4>
                <li>
                    <a href="logout.php" onclick="return confirmLogout()"><span class="material-symbols-outlined">
                            logout </span>Logout</a>
                </li>
            </ul>
            <div class="user-account">
                <div class="user-profile">
                    <img src="<?php echo $_SESSION['user']['img']; ?>" alt="Profile Image" />
                    <div class="user-detail">
                        <h3><?php echo $_SESSION['user']['nickname']; ?></h3>
                        <span><?php echo ucfirst($_SESSION['user']['role']); ?></span>
                    </div>
                </div>
            </div>

        </aside>

        <!-- Main content -->
        <div class="col-md-9" id="mainContent">

        </div>
    </div>

    <script>
        var newClassFormHtml = <?php echo json_encode($newClassFormHtml); ?>;

        function confirmLogout() {
            return confirm("Are you sure you want to logout?");
        }

        function showManageClass() {
            var mainContent = document.getElementById('mainContent');
            mainContent.innerHTML = `
    <style>
        h1 { margin-top: 20px; }
        .smaller-table { font-size: 0.8em; width: 80%; margin: auto; }
        .smaller-table th, .smaller-table td { padding: 5px; }
        .add-class-btn { margin: 20px auto; display: flex; justify-content: center; }
    </style>
        <h1 style="text-align: center;">List of Students</h1>
        <div class="add-class-btn">
            <button id="addClassBtn" class="btn btn-primary">Add Class</button>
        </div>
        <table id="studentTable" class="table table-striped smaller-table" style="width:100%">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Full Name</th>
                    <th scope="col">Age</th>
                    <th scope="col">Gender</th>
                    <th scope="col">Birthdate</th>
                    <th scope="col">Address</th>
                    <th scope="col">Year Level</th>
                    <th scope="col">Section</th>
                    <th scope="col">Subject</th>
                    <th scope="col">Email</th>
                    <th scope="col">LRN</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    `;

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'fetch_dteach.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send();

            xhr.onload = function () {
                if (this.status == 200) {
                    var students = JSON.parse(this.responseText);
                    var tbody = document.querySelector('#studentTable tbody');

                    for (var i = 0; i < students.length; i++) {
                        var tr = document.createElement('tr');
                        var fullname = `${students[i].fname} ${students[i].mname} ${students[i].lname}`;
                        tr.innerHTML = `
                    <th scope="row">${i + 1}</th>
                    <td>${fullname}</td>
                    <td>${students[i].age}</td>
                    <td>${students[i].gender}</td>
                    <td>${students[i].birthdate}</td>
                    <td>${students[i].address}</td>
                    <td>${students[i].year_level}</td>
                    <td>${students[i].section}</td>
                    <td>${students[i].subjects || 'No subjects'}</td>
                    <td>${students[i].email}</td>
                    <td>${students[i].lrn}</td>
                `;
                        tbody.appendChild(tr);
                    }

                    $('#studentTable').DataTable();
                }
            };

            document.getElementById('addClassBtn').addEventListener('click', showNewClass);
        }
        function showNewClass() {
            var mainContent = document.getElementById('mainContent');
            mainContent.innerHTML = newClassFormHtml;

            var form = document.getElementById('adminForm');
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                var formData = new FormData(this);

                fetch('subjectform.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    showManageClass();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while processing your request.'
                        });
                    });
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            showManageClass();
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.0.8/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>