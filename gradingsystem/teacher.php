<?php
session_start();
if ($_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}
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
                <img src="logo.jpg" alt="logo" />
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

        function confirmLogout() {
            var r = confirm("Are you sure you want to logout?");
            if (r == true) {
                // User confirmed they want to logout
                return true;
            } else {
                // User cancelled the logout operation
                return false;
            }
        }

        function showAddOptions() {
            // Implement logic to display options (e.g., a dropdown menu)
            // You can use CSS to show/hide the relevant elements.
            console.log("Showing add options: Add Student, Teacher, Admin");
            // Add your specific logic here...
        }
        function logout() {
            // Your logout logic here (e.g., redirect to logout page)
            window.location.href = "logout.php";
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

    // Fetch the student data from the server using AJAX
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'fetch_dteach.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send();

    xhr.onload = function () {
        if (this.status == 200) {
            // Parse the JSON data
            var students = JSON.parse(this.responseText);

            // Get the table body
            var tbody = document.querySelector('#studentTable tbody');

            // Insert the student data into the table
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
                    <td>${students[i].subject_name || 'No subject'}</td>
                    <td>${students[i].email}</td>
                    <td>${students[i].lrn}</td>
                `;
                tbody.appendChild(tr);
            }

            // Initialize DataTable
            $('#studentTable').DataTable();
        }
    };

    // Add event listener for "Add Class" button
    document.getElementById('addClassBtn').addEventListener('click', showNewClass);
}

// Ensure the function is called to render the manage class table on page load
document.addEventListener('DOMContentLoaded', function () {
    showManageClass();
});

function showNewClass() {
    var mainContent = document.getElementById('mainContent');
    mainContent.innerHTML = `
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
                            <select class="form-control item" id="year_level" name="year_level">
                                <option value="Grade 9">Grade 9</option>
                                <option value="Grade 10">Grade 10</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="section">Section assigned:</label>
                            <input type="text" class="form-control item" id="section" name="section" placeholder="Section this subject will be applied." required>
                        </div>
                    </div>
                    <input type="hidden" id="created_by" name="created_by" value="<?php echo $_SESSION['user_id']; ?>">
                    <!-- Ensure PHP tags are processed correctly -->
                    <div class="col-md-8">
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Create Class</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    `;

    // Get the form element
    var form = document.getElementById('adminForm');

    // Attach a submit event handler to the form
    form.addEventListener('submit', function (event) {
        // Prevent the form from being submitted normally
        event.preventDefault();

        // Create a new FormData object from the form
        var formData = new FormData(form);

        // Use AJAX to submit the form data
        var request = new XMLHttpRequest();
        request.open('POST', 'subjectform.php');
        request.onreadystatechange = function () {
            if (request.readyState === 4 && request.status === 200) {
                // The request has completed successfully
                var response = JSON.parse(request.responseText);
                if (response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
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
                        text: response.message
                    });
                }

                // Reset the form fields
                form.reset();
            }
        };
        request.send(formData);
    });
}


    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.0.8/datatables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>