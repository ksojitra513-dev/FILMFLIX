<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - FLIMFLIX</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Register Page Wala Background aur Body Styling */
        html, body {
            background-color: #000000 !important;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }
        body {
            /* Background image vahi rakhi hai jo register me thi */
            background: black url('all 1.jpg') no-repeat center center fixed !important;
            background-size: cover !important;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Register Box (White Card) Styling */
        .contact-box {
            width: 100%;
            max-width: 500px;
            background-color: rgba(255, 255, 255, 0.95) !important;
            border-radius: 15px !important;
            border: none !important;
            color: #333;
            overflow: hidden; /* Header radius ke liye */
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            margin: 20px;
        }

        /* Red Header like Register Page */
        .card-header-custom {
            background-color: #dc3545 !important;
            padding: 20px;
            color: white !important;
            text-align: center;
        }

        .form-padding {
            padding: 30px;
        }

        .form-label { color: #333; font-weight: bold; }
        
        /* Validation Error Styling */
        .error {
            color: #dc3545;
            font-size: 0.85rem;
            font-weight: 500;
            margin-top: 5px;
            display: block;
        }
        .is-invalid {
            border-color: #dc3545 !important;
        }
    </style>
</head>

<body>
    
<?php include 'hhh.php'; ?>

<div class="contact-box">
    <div class="card-header-custom">
        <h4 class="mb-0 fw-bold">Contact Us</h4>
    </div>

    <div class="form-padding">
        <form action="contact2.php" method="POST" id="contactForm">
            
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="FullName" class="form-control" placeholder="Your Name">
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="name@example.com">
            </div>

            <div class="mb-3">
                <label class="form-label">Message</label>
                <textarea name="Message" rows="4" class="form-control" placeholder="How can we help you?"></textarea>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-danger btn-lg fw-bold">
                    Send Message
                </button>
            </div>

        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.0/jquery.validate.min.js"></script>

<script>
$(document).ready(function() {
    $("#contactForm").validate({
        rules: {
            FullName: { 
                required: true, 
                minlength: 20 
            },
            email: { 
                required: true, 
                email: true 
            },
            Message: { 
                required: true, 
                minlength: 20 
            }
        },
        messages: {
            FullName: {
                required: "please Enter a fullname.",
                minlength: "Naam kam se kam 3 characters ka ho."
            },
            email: {
                required: "please Enter a Email Address.",
                email: "Valid email enter karein."
            },
            Message: {
                required: "please Enter a Message.",
                minlength: "Message thoda bada likhein (min 10 chars)."
            }
        },
        errorElement: 'div',
        errorPlacement: function (error, element) {
            error.addClass('error');
            element.closest('.mb-3').append(error);
        },
        highlight: function (element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
        }
    });
});

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>