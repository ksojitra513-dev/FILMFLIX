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
        .form-container { padding-top: 100px; padding-bottom: 50px; }
        .card { background-color: rgba(255, 255, 255, 0.95) !important; border-radius: 15px !important; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .card-header { background-color: #dc3545 !important; border-radius: 15px 15px 0 0 !important; color: white !important; }
        .form-label { color: #333; font-weight: bold; }
        .error { color: #dc3545 !important; font-size: 0.85rem; font-weight: bold; display: block; margin-top: 5px; }
        input.error, select.error { border: 2px solid #dc3545 !important; }
    </style>
</head>
<body>
    

    <div class="container form-container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card shadow-lg">
                    <div class="card-header text-center py-3">
                        <h4 class="mb-0 fw-bold">User Registration</h4>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <form id="registrationForm" action="88_r2.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="fullname" class="form-control" placeholder="Min 10 characters">
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
                                <input type="date" name="birthdate" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label d-block">Hobbies</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="genres[]" value="dancing">
                                    <label class="form-check-label">Dancing</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="genres[]" value="reading">
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
                            <div class="text-center mt-4">
                            <p class="text-muted mb-0">Already have an account?</p>
                            <a href="login.php" class="text-danger fw-bold text-decoration-none shadow-sm" style="font-size: 1.1rem;">
                            Login Here
                            </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
    $(document).ready(function() {
        $("#registrationForm").validate({
            rules: {
                fullname: { required: true, minlength: 10 },
                email: { required: true, email: true },
                number: { required: true, digits: true, minlength: 10, maxlength: 10 },
                password: { required: true, minlength: 8 },
                birthdate: { required: true },
                "genres[]": { required: true },
                City: { required: true }
            },
            messages: {
                FullName: "Please enter your full name (min 10 chars).",
                password: "Password must be at least 8 characters."
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "genres[]") error.appendTo("#hobby-error");
                else error.insertAfter(element);
            },
            submitHandler: function(form) {
                // Pehle form submit hoga database ke liye
                form.submit(); 
            }
        });
    });
    </script>
</body>
</html>