<?php 
include_once 'config.php';

// ---------------------------------------------------------
// BACKEND LOGIC
// ---------------------------------------------------------

// 1. Fetch User Data (AJAX for Edit & View)
if(isset($_GET['get_user_data'])) {
    $u_id = mysqli_real_escape_string($con, $_GET['get_user_data']);
    $query = "SELECT * FROM registration WHERE id='$u_id'";
    $query_run = mysqli_query($con, $query);

    if(mysqli_num_rows($query_run) == 1) {
        $user = mysqli_fetch_array($query_run);
        echo json_encode(['status' => 200, 'data' => $user]);
        exit;
    }
}

// 2. Delete Logic
if(isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    mysqli_query($con, "DELETE FROM registration WHERE id='$id'");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// 3. Update Logic
if(isset($_POST['update_btn'])) {
    $id = $_POST['user_id'];
    $name = mysqli_real_escape_string($con, $_POST['fullname']);
    $city = mysqli_real_escape_string($con, $_POST['city']);
    $num = mysqli_real_escape_string($con, $_POST['number']);

    mysqli_query($con, "UPDATE registration SET fullname='$name', city='$city', number='$num' WHERE id='$id'");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Management - FILMFLIX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #fff; padding: 20px; }
        .main-title { text-align: center; font-size: 45px; font-weight: 500; margin-bottom: 30px; }
        .controls-wrapper { display: flex; gap: 15px; margin-bottom: 20px; }
        .search-input { flex-grow: 1; padding: 10px; border: 1px solid #b4d3ff; border-radius: 6px; outline: none; }
        
        /* Table Styling */
        .custom-table { width: 100%; border-collapse: collapse; border: 1px solid #dee2e6; }
        .custom-table th, .custom-table td { border: 1px solid #dee2e6; padding: 12px; vertical-align: top; font-size: 14px; }
        .user-thumb { width: 60px; height: 60px; object-fit: cover; border: 1px solid #eee; display: block; margin: 0 auto; }

        /* Action Buttons */
        .action-stack { display: flex; flex-direction: column; gap: 4px; width: 95px; }
        .btn-act { border: none; border-radius: 3px; color: white; padding: 5px; font-size: 12px; text-align: center; cursor: pointer; text-decoration: none; }
        .v-blue { background: #007bff; }
        .e-yellow { background: #ffc107; color: black; }
        .d-red { background: #dc3545; }

        /* Modal Headers */
        .modal-header-yellow { background-color: #ffc107; color: #000; }
        .modal-header-blue { background-color: #007bff; color: #fff; }
    </style>
</head>
<body>

    <h1 class="main-title">Profile Management</h1>

    <div class="controls-wrapper">
        <button class="btn btn-success">Add New Profile</button>
        <input type="text" id="searchProfile" class="search-input" placeholder="Search profiles...">
    </div>

    <table class="custom-table" id="profileTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>City</th>
                <th>Image</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $res = mysqli_query($con, "SELECT * FROM registration");
            while($user = mysqli_fetch_assoc($res)) {
                $img = !empty($user['image']) ? "uploads/".$user['image'] : "https://ui-avatars.com/api/?name=".urlencode($user['fullname']);
            ?>
            <tr>
                <td><?= $user['id']; ?></td>
                <td><strong><?= $user['fullname']; ?></strong></td>
                <td><?= $user['email']; ?></td>
                <td><?= $user['number']; ?></td>
                <td><?= $user['city']; ?></td>
                <td><img src="<?= $img; ?>" class="user-thumb"></td>
                <td><span class="text-success fw-bold">Active</span></td>
                <td>
                    <div class="action-stack">
                        <button class="btn-act v-blue viewBtn" value="<?= $user['id']; ?>">View</button>
                        <button class="btn-act e-yellow editBtn" value="<?= $user['id']; ?>">Edit</button>
                        <a href="?delete_id=<?= $user['id']; ?>" class="btn-act d-red" onclick="return confirm('Kya aap is profile ko delete karna chahte hain?')">Delete</a>
                    </div>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="modal fade" id="viewProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-blue">
                    <h5 class="modal-title">User Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" id="view_img" class="rounded-circle mb-3" style="width: 120px; height: 120px; border: 3px solid #007bff; object-fit: cover;">
                    <h4 id="view_name" class="fw-bold"></h4>
                    <hr>
                    <div class="text-start px-3">
                        <p><strong>Email:</strong> <span id="view_email"></span></p>
                        <p><strong>Phone:</strong> <span id="view_phone"></span></p>
                        <p><strong>City:</strong> <span id="view_city"></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-yellow">
                    <h5 class="modal-title" id="editModalTitle">Edit Profile: ...</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body p-4">
                        <input type="hidden" name="user_id" id="user_id">
                        <div class="mb-3">
                            <label class="fw-bold small">Full Name</label>
                            <input type="text" name="fullname" id="fullname" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small">City</label>
                                <input type="text" name="city" id="city" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="fw-bold small">Mobile Number</label>
                                <input type="text" name="number" id="number" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold small">Description</label>
                            <textarea class="form-control" rows="3">Active member of FILMFLIX dashboard. Registered user since 2024.</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_btn" class="btn btn-warning fw-bold">Update Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // VIEW Button Logic
            $('.viewBtn').on('click', function() {
                var u_id = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "<?php echo $_SERVER['PHP_SELF']; ?>?get_user_data=" + u_id,
                    success: function (response) {
                        var res = JSON.parse(response);
                        if(res.status == 200) {
                            $('#view_name').text(res.data.fullname);
                            $('#view_email').text(res.data.email);
                            $('#view_phone').text(res.data.number);
                            $('#view_city').text(res.data.city);
                            $('#view_img').attr('src', 'uploads/' + res.data.image);
                            $('#viewProfileModal').modal('show');
                        }
                    }
                });
            });

            // EDIT Button Logic
            $('.editBtn').on('click', function() {
                var u_id = $(this).val();
                $.ajax({
                    type: "GET",
                    url: "<?php echo $_SERVER['PHP_SELF']; ?>?get_user_data=" + u_id,
                    success: function (response) {
                        var res = JSON.parse(response);
                        if(res.status == 200) {
                            $('#user_id').val(res.data.id);
                            $('#fullname').val(res.data.fullname);
                            $('#city').val(res.data.city);
                            $('#number').val(res.data.number);
                            $('#editModalTitle').text('Edit Profile: ' + res.data.fullname);
                            $('#editProfileModal').modal('show');
                        }
                    }
                });
            });

            // Search Filter
            $("#searchProfile").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#profileTable tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
</body>
</html>