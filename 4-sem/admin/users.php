<?php
require_once 'includes/auth.php';
require_once '../config.php';



$msg = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
        $email    = mysqli_real_escape_string($con, $_POST['email']);
        $number   = mysqli_real_escape_string($con, $_POST['number']);
        $birthdate= mysqli_real_escape_string($con, $_POST['birthdate']);
        $city     = mysqli_real_escape_string($con, $_POST['city']);
        $role     = mysqli_real_escape_string($con, $_POST['role']);
        $status   = mysqli_real_escape_string($con, $_POST['status']);
        $image    = mysqli_real_escape_string($con, $_POST['image'] ?? '');
        $genres   = mysqli_real_escape_string($con, $_POST['genres'] ?? '');
        $password = $_POST['password'];

        $sql = "INSERT INTO user (fullname,email,number,birthdate,city,role,status,password,image,genres)
                VALUES ('$fullname','$email','$number','$birthdate','$city','$role','$status','$password','$image','$genres')";
        if (mysqli_query($con, $sql)) $msg = ['type'=>'success','text'=>'User added successfully!'];
        else $msg = ['type'=>'error','text'=>'Error: '.mysqli_error($con)];
    }

    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        if (mysqli_query($con, "DELETE FROM user WHERE id=$id"))
            $msg = ['type'=>'success','text'=>'User deleted.'];
        else $msg = ['type'=>'error','text'=>'Delete failed.'];
    }

    if ($action === 'update_status') {
        $id = (int)$_POST['id'];
        $status = $_POST['status'] === 'active' ? 'inactive' : 'active';
        mysqli_query($con, "UPDATE user SET status='$status' WHERE id=$id");
        $msg = ['type'=>'success','text'=>'Status updated.'];
    }

    if ($action === 'edit') {
        $id = (int)$_POST['id'];
        $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
        $email    = mysqli_real_escape_string($con, $_POST['email']);
        $number   = mysqli_real_escape_string($con, $_POST['number']);
        $birthdate= mysqli_real_escape_string($con, $_POST['birthdate']);
        $city     = mysqli_real_escape_string($con, $_POST['city']);
        $role     = mysqli_real_escape_string($con, $_POST['role']);
        $status   = mysqli_real_escape_string($con, $_POST['status']);
        $image    = mysqli_real_escape_string($con, $_POST['image'] ?? '');
        $genres   = mysqli_real_escape_string($con, $_POST['genres'] ?? '');
        
        $pw_q = "";
        if(!empty($_POST['password'])) {
            $password = $_POST['password'];
            $pw_q = ", password='$password'";
        }

        $sql = "UPDATE user SET fullname='$fullname', email='$email', number='$number', birthdate='$birthdate', city='$city', role='$role', status='$status', image='$image', genres='$genres' $pw_q WHERE id=$id";
        if (mysqli_query($con, $sql)) $msg = ['type'=>'success','text'=>'User updated successfully!'];
        else $msg = ['type'=>'error','text'=>'Update failed: '.mysqli_error($con)];
    }
}

$users = mysqli_query($con, "SELECT * FROM user ORDER BY created_at DESC");
$total = mysqli_num_rows($users);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FilmFlix Admin — Users</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<?php include 'includes/sidebar.php'; ?>
<div class="main-content">
  <?php include 'includes/topbar.php'; ?>
  <div class="page-content">

    <div class="page-header">
      <div>
        <h1 class="page-title">User Management</h1>
        <p class="page-subtitle"><?= $total ?> registered users</p>
      </div>
      <button class="btn btn-primary" onclick="openModal('addModal')"><i class="fas fa-plus"></i> Add User</button>
    </div>

    <?php if($msg): ?>
    <div class="alert alert-<?= $msg['type'] ?>"><i class="fas fa-<?= $msg['type']==='success'?'check-circle':'times-circle' ?>"></i><?= $msg['text'] ?></div>
    <?php endif; ?>

    <div class="content-card">
      <div class="toolbar">
        <div class="search-box"><i class="fas fa-search"></i><input type="text" id="userSearch" class="form-control" placeholder="Search users..."></div>
        <button class="btn btn-secondary" onclick="exportTableToCSV('usersTable', 'users_export.csv')"><i class="fas fa-file-export"></i> Export</button>
      </div>
      <div class="table-wrapper" style="margin-top:20px;">
        <table class="data-table" id="usersTable">
          <thead><tr>
            <th>id</th>
            <th>image</th>
            <th>fullname</th>
            <th>email</th>
            <th>number</th>
            <th>birthdate</th>
            <th>password</th>
            <th>genres</th>
            <th>city</th>
            <th>role</th>
            <th>status</th>
            <th>created_at</th>
            <th>Actions</th>
          </tr></thead>
          <tbody>
          <?php while($u = mysqli_fetch_assoc($users)): ?>
          <tr>
            <td><span class="booking-id"><?= $u['id'] ?></span></td>
            <td><?= !empty($u['image']) ? htmlspecialchars($u['image']) : '<span style="color:var(--text-muted); font-style:italic;">NULL</span>' ?></td>
            <td style="max-width:120px; white-space:normal; line-height:1.4; color:var(--text-primary); font-weight:500;">
               <?= htmlspecialchars($u['fullname']) ?>
            </td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['number']) ?></td>
            <td><?= !empty($u['birthdate']) ? htmlspecialchars($u['birthdate']) : '<span style="color:var(--text-muted); font-style:italic;">NULL</span>' ?></td>
            <td style="max-width:150px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-family:monospace; font-size:12px;" title="<?= htmlspecialchars($u['password']) ?>">
               <?= htmlspecialchars($u['password']) ?>
            </td>
            <td><?= !empty($u['genres']) ? htmlspecialchars($u['genres']) : '<span style="color:var(--text-muted); font-style:italic;">NULL</span>' ?></td>
            <td><?= !empty($u['city']) ? htmlspecialchars($u['city']) : '<span style="color:var(--text-muted); font-style:italic;">NULL</span>' ?></td>
            <td><span class="status-badge <?= $u['role']==='admin'?'paid':'pending' ?>"><?= ucfirst($u['role']) ?></span></td>
            <td><span class="status-badge <?= strtolower($u['status']) ?>"><?= ucfirst($u['status']) ?></span></td>
            <td style="font-size:12px;"><?= htmlspecialchars($u['created_at']) ?></td>
            <td>
              <div style="display:flex;flex-wrap:nowrap;gap:6px;">
                <button type="button" class="btn btn-sm btn-primary btn-icon" title="Edit" onclick="openEditUserModal(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['fullname'])) ?>', '<?= htmlspecialchars(addslashes($u['email'])) ?>', '<?= htmlspecialchars(addslashes($u['number'])) ?>', '<?= htmlspecialchars(addslashes($u['city'] ?? '')) ?>', '<?= $u['role'] ?>', '<?= $u['status'] ?>', '<?= $u['birthdate'] ?>', '<?= htmlspecialchars(addslashes($u['image'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($u['genres'] ?? '')) ?>')"><i class="fas fa-edit"></i></button>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete user?')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $u['id'] ?>">
                  <button type="submit" class="btn btn-sm btn-danger btn-icon"><i class="fas fa-trash"></i></button>
                </form>
              </div>
            </td>
          </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<!-- Add User Modal -->
<div class="modal-overlay" id="addModal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title"><i class="fas fa-user-plus" style="color:#a855f7;margin-right:8px;"></i>Add New User</span>
      <button class="modal-close" onclick="closeModal('addModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" id="addUserForm" novalidate>
      <input type="hidden" name="action" value="add">
      <div class="modal-body">
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" name="fullname" class="form-control" placeholder="John Doe">
          </div>
          <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" placeholder="john@example.com">
          </div>
          <div class="form-group">
            <label class="form-label">Phone</label>
            <input type="text" name="number" class="form-control" placeholder="9876543210">
          </div>
          <div class="form-group">
            <label class="form-label">Birth Date</label>
            <input type="date" name="birthdate" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">Password</label>
            <div class="password-wrapper">
               <input type="password" name="password" class="form-control" placeholder="••••••••">
               <button type="button" class="toggle-password" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">City</label>
            <input type="text" name="city" class="form-control" placeholder="Rajkot">
          </div>
          <div class="form-group">
            <label class="form-label">Profile Image Name (e.g. default.png)</label>
            <input type="text" name="image" class="form-control" placeholder="default.png">
          </div>
          <div class="form-group">
            <label class="form-label">Genres (Comma separated)</label>
            <input type="text" name="genres" class="form-control" placeholder="reading, dancing">
          </div>
          <div class="form-group">
            <label class="form-label">Role</label>
            <select name="role" class="form-control">
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">Cancel</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
      </div>
    </form>
  </div>
</div>

<!-- View User Modal -->
<div class="modal-overlay" id="viewUserModal">
  <div class="modal" style="max-width:500px;">
    <div class="modal-header">
      <span class="modal-title">User Profile Overview</span>
      <button class="modal-close" onclick="closeModal('viewUserModal')"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body">
       <div style="display:flex; align-items:center; gap:20px; margin-bottom:25px; padding-bottom:20px; border-bottom:1px solid var(--border);">
          <div id="v_avatar" style="width:70px; height:70px; border-radius:50%; background:linear-gradient(135deg,#a855f7,#06b6d4); display:flex; align-items:center; justify-content:center; color:#fff; font-size:30px; font-weight:800; text-shadow:0 2px 4px rgba(0,0,0,0.3);"></div>
          <div>
             <h2 id="v_name" style="font-size:22px; font-weight:800; color:#fff; margin-bottom:4px;"></h2>
             <div id="v_badge_wrap" style="display:flex; gap:6px;">
                <span id="v_role" class="status-badge"></span>
                <span id="v_status" class="status-badge"></span>
             </div>
          </div>
       </div>
       <div class="form-grid">
          <div class="form-group">
             <label class="form-label">Email Address</label>
             <div id="v_email" style="font-size:14px; color:var(--text-primary); font-weight:500;"></div>
          </div>
          <div class="form-group">
             <label class="form-label">Phone Number</label>
             <div id="v_phone" style="font-size:14px; color:var(--text-primary); font-weight:500;"></div>
          </div>
          <div class="form-group">
             <label class="form-label">City / Location</label>
             <div id="v_city" style="font-size:14px; color:var(--text-primary); font-weight:500;"></div>
          </div>
          <div class="form-group">
             <label class="form-label">Joined On</label>
             <div id="v_joined" style="font-size:14px; color:var(--text-primary); font-weight:500;"></div>
          </div>
       </div>
    </div>
    <div class="modal-footer">
       <button type="button" class="btn btn-primary" style="width:100%; justify-content:center;" onclick="closeModal('viewUserModal')">Done</button>
    </div>
  </div>
</div>

<!-- Edit User Modal -->
<div class="modal-overlay" id="editModal">
  <div class="modal">
    <div class="modal-header">
      <span class="modal-title">Edit Administrative User</span>
      <button class="modal-close" onclick="closeModal('editModal')"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" id="editUserForm" novalidate>
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="id" id="edit_user_id">
      <div class="modal-body">
        <div class="form-grid">
          <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" name="fullname" id="edit_fullname" class="form-control" placeholder="John Doe">
          </div>
          <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" id="edit_email" class="form-control" placeholder="john@example.com">
          </div>
          <div class="form-group">
            <label class="form-label">Mobile Number</label>
            <input type="text" name="number" id="edit_number" class="form-control" placeholder="+91 0000000000">
          </div>
          <div class="form-group">
            <label class="form-label">Birth Date</label>
            <input type="date" name="birthdate" id="edit_birthdate" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">New Password (Leave empty to keep current)</label>
            <div class="password-wrapper">
               <input type="password" name="password" class="form-control" placeholder="••••••••">
               <button type="button" class="toggle-password" onclick="togglePassword(this)"><i class="fas fa-eye"></i></button>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">City</label>
            <input type="text" name="city" id="edit_city" class="form-control" placeholder="Rajkot">
          </div>
          <div class="form-group">
            <label class="form-label">Profile Image Name</label>
            <input type="text" name="image" id="edit_image" class="form-control" placeholder="default.png">
          </div>
          <div class="form-group">
            <label class="form-label">Genres</label>
            <input type="text" name="genres" id="edit_genres" class="form-control" placeholder="reading, dancing">
          </div>
          <div class="form-group">
            <label class="form-label">Role</label>
            <select name="role" id="edit_role" class="form-control">
              <option value="user">User</option>
              <option value="admin">Administrator</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" id="edit_status" class="form-control">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancel</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
      </div>
    </form>
  </div>
</div>

<script src="assets/admin.js"></script>
<script>
function openViewUserModal(name, email, phone, city, role, status, joined) {
    document.getElementById('v_name').innerText = name;
    document.getElementById('v_email').innerText = email;
    document.getElementById('v_phone').innerText = phone || 'Not provided';
    document.getElementById('v_city').innerText = city || 'Not specified';
    document.getElementById('v_joined').innerText = joined;
    document.getElementById('v_avatar').innerText = name.charAt(0).toUpperCase();
    
    // Status badges
    const vRole = document.getElementById('v_role');
    vRole.innerText = role;
    vRole.className = `status-badge ${role === 'admin' ? 'paid' : 'pending'}`;
    
    const vStatus = document.getElementById('v_status');
    vStatus.innerText = status;
    vStatus.className = `status-badge ${status === 'active' ? 'active' : 'inactive'}`;
    
    openModal('viewUserModal');
}

function openEditUserModal(id, name, email, phone, city, role, status, bdate, image, genres) {
    document.getElementById('edit_user_id').value = id;
    document.getElementById('edit_fullname').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_number').value = phone;
    document.getElementById('edit_city').value = city;
    document.getElementById('edit_role').value = role;
    document.getElementById('edit_status').value = status;
    document.getElementById('edit_birthdate').value = bdate;
    document.getElementById('edit_image').value = image || '';
    document.getElementById('edit_genres').value = genres || '';
    openModal('editModal');
}

function exportTableToCSV(tableID, filename) {
    let csv = [];
    let rows = document.querySelectorAll("#" + tableID + " tr");
    for (let i = 0; i < rows.length; i++) {
        let row = [], cols = rows[i].querySelectorAll("td, th");
        for (let j = 0; j < cols.length - 1; j++) {
            let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, "").trim();
            data = data.replace(/"/g, '""');
            row.push('"' + data + '"');
        }
        csv.push(row.join(","));
    }
    let csvString = csv.join("\n");
    let link = document.createElement("a");
    link.style.display = 'none';
    link.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvString));
    link.setAttribute('download', filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

filterTable('userSearch','usersTable');

document.addEventListener('DOMContentLoaded', () => {
    const userForms = [document.getElementById('addUserForm'), document.getElementById('editUserForm')];
    
    userForms.forEach(form => {
        if(!form) return;

        const inputs = form.querySelectorAll('.form-control');
        
        const validationRules = {
            fullname: {
                required: true,
                min: 3,
                pattern: /^[a-zA-Z\s]{3,}$/,
                error: "Full name must be at least 3 letters."
            },
            email: {
                required: true,
                pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                error: "A valid email address is required (e.g. name@domain.com)."
            },
            number: {
                required: true,
                pattern: /^[0-9]{10}$/,
                error: "A valid 10-digit phone number is required."
            },
            birthdate: {
                required: true,
                error: "Please select a valid birth date."
            },
            password: {
                required: form.id === 'addUserForm',
                min: 6,
                error: "Password must be at least 6 characters."
            },
            city: {
                required: true,
                min: 2,
                error: "Valid city name is required."
            },
            image: {
                required: false,
                min: 4,
                error: "Image name too short (e.g. img.png)."
            },
            genres: {
                required: false,
                min: 2,
                error: "Genres list too short."
            }
        };

        const validateField = (input) => {
            const name = input.getAttribute('name');
            const rule = validationRules[name];
            if(!rule) return true;

            const val = input.value.trim();
            const parent = input.closest('.form-group');
            
            // Clear existing
            input.style.borderColor = '';
            if (parent) {
                const err = parent.querySelector('.error-msg');
                if (err) err.remove();
            }

            // Required check
            if (rule.required && val === '') {
                showError(input, rule.error || `${name} is required.`);
                return false;
            }

            // Length check
            if (val !== '' && rule.min && val.length < rule.min) {
                showError(input, rule.error);
                return false;
            }

            // Pattern check
            if (val !== '' && rule.pattern && !rule.pattern.test(val)) {
                showError(input, rule.error);
                return false;
            }
            
            return true;
        };

        const showError = (el, msg) => {
            el.style.borderColor = '#ef4444';
            const parent = el.closest('.form-group');
            if (parent && !parent.querySelector('.error-msg')) {
                const err = document.createElement('small');
                err.className = 'error-msg';
                err.innerText = msg;
                parent.appendChild(err);
            }
        };

        // Event Listeners for Real-time Feedback
        inputs.forEach(input => {
            ['input', 'blur'].forEach(evt => {
                input.addEventListener(evt, () => validateField(input));
            });
        });

        // Submit Handler
        form.addEventListener('submit', function(e) {
            let isFormValid = true;
            let firstInvalid = null;

            inputs.forEach(input => {
                if (!validateField(input)) {
                    isFormValid = false;
                    if (!firstInvalid) firstInvalid = input;
                }
            });

            if (!isFormValid) {
                e.preventDefault();
                if (firstInvalid) firstInvalid.focus();
                showToast('Please correct the highlighted errors.', 'error');
            }
        });
    });
});

function toggleTablePassword(btn, id, val) {
    const span = document.getElementById('pass_' + id);
    const icon = btn.querySelector('i');
    if (span.innerText === '••••••••') {
        span.innerText = val;
        span.style.letterSpacing = 'normal';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        span.innerText = '••••••••';
        span.style.letterSpacing = '2px';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

function togglePassword(btn) {
    const input = btn.previousElementSibling;
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
</body>
</html>


