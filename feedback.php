<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FILMFLIX - Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #0d0d0d;
            background-image: radial-gradient(circle at 50% -20%, #3d0000 0%, #000000 80%);
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .card {
            background: rgba(20, 20, 20, 0.9);
            border: 1px solid #333;
            border-radius: 15px;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 10px 30px rgba(229, 9, 20, 0.2);
        }

        .card-header {
    font-size: 1.8rem;
    font-weight: 800;
    text-align: center;

    /* gradient text */
    background: linear-gradient(to right, #ff4d4d, #e50914);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

        

        .rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
            gap: 10px;
        }
        .rating input { display: none; }
        .rating label {
            cursor: pointer;
            width: 40px;
            height: 40px;
            background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" fill="%23333" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>');
            background-repeat: no-repeat;
            background-position: center;
            transition: .3s;
        }
        .rating input:checked ~ label,
        .rating label:hover,
        .rating label:hover ~ label {
            background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" fill="%23e50914" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>');
        }

        .form-control {
            background: #1a1a1a !important;
            border: 1px solid #333;
            color: #fff !important;
            border-radius: 10px;
        }
        .form-control:focus {
            border-color: #e50914;
            box-shadow: 0 0 10px rgba(229, 9, 20, 0.5);
        }

        .btn-submit {
            background: #e50914;
            border: none;
            color: #fff;
            font-weight: bold;
            padding: 12px;
            border-radius: 10px;
            transition: 0.3s;
        }
        .btn-submit:hover {
            background: #ff0f1b;
            transform: scale(1.02);
        }

        /* Error messages */
        .error {
            color: #ff4d4d;
            font-size: 0.9rem;
            margin-top: 5px;
            display: block;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="main-container">
    <div class="card shadow">
        <div class="card-header">Give Feedback</div>
        <div class="card-body px-4 pb-4">
            <form action="save_feedback.php" method="POST" id="feedbackForm">
                
                <div class="mb-4 text-center">
                    <label class="form-label d-block mb-3">How was your experience?</label>
                    <div class="rating">
                        <input type="radio" name="star" value="5" id="5"><label for="5"></label>
                        <input type="radio" name="star" value="4" id="4"><label for="4"></label>
                        <input type="radio" name="star" value="3" id="3"><label for="3"></label>
                        <input type="radio" name="star" value="2" id="2"><label for="2"></label>
                        <input type="radio" name="star" value="1" id="1"><label for="1"></label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Subject</label>
                    <input type="text" name="subject" class="form-control" placeholder="E.g. Streaming quality, New Movies">
                </div>

                <div class="mb-3">
                    <label class="form-label">Your Message</label>
                    <textarea name="message" class="form-control" rows="4" placeholder="Write your thoughts here..."></textarea>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn-red btn-submit">Send Feedback</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- jQuery & Validation -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

<script>
$(document).ready(function() {
    $("#feedbackForm").validate({
        rules: {
            star: {
                required: true
            },
            subject: {
                required: true,
                minlength: 3
            },
            message: {
                required: true,
                minlength: 10
            }
        },
        messages: {
            star: "Please select a rating.",
            subject: {
                required: "Please enter a subject.",
                minlength: "Subject must be at least 3 characters."
            },
            message: {
                required: "Please enter your message.",
                minlength: "Message must be at least 10 characters."
            }
        },
        errorPlacement: function(error, element) {
            if (element.attr("name") == "star") {
                error.insertAfter(".rating");
            } else {
                error.insertAfter(element);
            }
        },
       


             submitHandler: function(form) {
            // 1. Pehle Success Message dikhayega
            alert("Thank you for feedback...");

             window.location.href = "c.php"; 
        }
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
