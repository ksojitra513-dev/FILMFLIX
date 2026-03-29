<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FLIMFLIX - Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body { background-color: #000; margin: 0; padding: 0; min-height: 100vh; font-family: 'Segoe UI', sans-serif; }
        body { background: black url('all 1.jpg') no-repeat center center fixed !important; background-size: cover !important; }
        
        .form-container { padding-top: 130px; padding-bottom: 50px; }
        
        .card { background-color: rgba(255, 255, 255, 0.95) !important; border-radius: 15px !important; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .card-header { background-color: #dc3545 !important; border-radius: 15px 15px 0 0 !important; color: white !important; }
        .form-label { color: #333; font-weight: bold; }
        
        /* Validation Error Styling */
        .error { color: #dc3545 !important; font-size: 0.85rem; font-weight: bold; display: block; margin-top: 5px; }
        .is-invalid { border: 2px solid #dc3545 !important; }
        
        .profile-upload-vh { position: relative; width: 110px; height: 110px; margin: 0 auto 10px; }
        #preview { width: 110px; height: 110px; object-fit: cover; border-radius: 50%; border: 3px solid #dc3545; background-color: #eee; }
        .upload-btn-wrapper { position: absolute; bottom: 0; right: 0; background: #dc3545; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 2px solid white; font-weight: bold; }
    </style>
</head>
<body>
    <?php include 'hhh.php'; ?>

    <div class="container form-container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card shadow-lg">
                    <div class="card-header text-center py-3">
                        <h4 class="mb-0 fw-bold">User Registration</h4>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <form id="registrationForm" action="r2.php" method="POST" enctype="multipart/form-data">
                            
                            <div class="text-center mb-4">
                                <div class="profile-upload-vh">
                                    <img id="preview" src="https://via.placeholder.com/110?text=User" alt="Profile Preview">
                                    <label for="profile_photo" class="upload-btn-wrapper"><span>+</span></label>
                                    <input type="file" id="profile_photo" name="profile_photo" class="d-none" accept="image/*" onchange="previewImage(this)">
                                </div>
                                <label class="form-label d-block small">Profile Photo (Required)</label>
                                <div id="photo-error"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="FullName" class="form-control" placeholder="Min 10 characters">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" placeholder="Email">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Contact No</label>
                                    <input type="text" name="number" class="form-control" placeholder="10 Digits">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Min 8 characters">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Birthdate</label>
                                <input type="date" name="Birthdate" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label d-block">Hobbies</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="hobbies[]" value="dancing">
                                    <label class="form-check-label">Dancing</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="hobbies[]" value="reading">
                                    <label class="form-check-label">Reading</label>
                                </div>
                                <div id="hobby-error"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Select City</label>
                                <select name="City" class="form-select">
                                    <option value="">-- Select City --</option>
                                    <option value="Rajkot">Rajkot</option>
                                    <option value="Bhavnagar">Bhavnagar</option>
                                </select>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" name="save_user" class="btn btn-danger btn-lg fw-bold">Register Now</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/additional-methods.min.js"></script>

    <script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) { $('#preview').attr('src', e.target.result); }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(document).ready(function() {
        $("#registrationForm").validate({
            rules: {
                profile_photo: { required: true, extension: "jpg|jpeg|png" },
                FullName: { required: true, minlength: 10 },
                email: { required: true, email: true },
                number: { required: true, digits: true, minlength: 10, maxlength: 10 },
                password: { required: true, minlength: 8 },
                Birthdate: { required: true },
                "hobbies[]": { required: true },
                City: { required: true }
            },
            messages: {
                profile_photo: { 
                    required: "Please select a profile photo.", 
                    extension: "Only JPG, JPEG, or PNG files are allowed." 
                },
                FullName: { 
                    required: "Please enter a fullname.", 
                    minlength: "Fullname must be at least 10 characters." 
                },
                email: { 
                    required: "Please enter your email address.", 
                    email: "Please enter a valid email." 
                },
                number: { 
                    required: "Please enter your contact number.", 
                    digits: "Only numbers are allowed.",
                    minlength: "Must be exactly 10 digits.",
                    maxlength: "Must be exactly 10 digits."
                },
                password: { 
                    required: "Please enter a password.", 
                    minlength: "Password must be at least 8 characters." 
                },
                Birthdate: { required: "Please select your birthdate." },
                "hobbies[]": { required: "Please select at least one hobby." },
                City: { required: "Please select your city." }
            },
            errorElement: 'div',
            errorPlacement: function(error, element) {
                error.addClass('error');
                if (element.attr("name") == "hobbies[]") error.appendTo("#hobby-error");
                else if (element.attr("name") == "profile_photo") error.appendTo("#photo-error");
                else error.insertAfter(element);
            },
            highlight: function(element) { $(element).addClass('is-invalid'); },
            unhighlight: function(element) { $(element).removeClass('is-invalid'); }
        });
    });
    </script>
</body>
</html>