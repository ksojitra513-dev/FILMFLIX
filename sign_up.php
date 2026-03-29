<!DOCTYPE html>
<html lang="gu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Sign In & Guest Mode</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* --- BIGGER & STYLISH SHINE BUTTON --- */
        .btn-shine-fx {
            position: relative;
            /* Size moti karva mate padding vadhaari (Top-Bottom 20px, Left-Right 60px) */
            padding: 20px 60px !important;
            /* Akshar mota karva mate */
            font-size: 24px !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            letter-spacing: 2px;
            background: linear-gradient(45deg, #e50914, #ff4500) !important;
            color: white !important;
            border: none !important;
            border-radius: 50px !important;
            /* Perfect Capsule Shape */
            overflow: hidden;
            transition: all 0.3s ease;
            z-index: 1;
            box-shadow: 0 10px 25px rgba(229, 9, 20, 0.4) !important;
        }

        /* Shine Effect (Same as before) */
        .btn-shine-fx::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -100%;
            width: 60%;
            height: 200%;
            background: rgba(255, 255, 255, 0.4);
            transform: rotate(25deg);
            animation: sweep 3.5s infinite;
        }

        @keyframes sweep {
            0% {
                left: -100%;
            }

            20% {
                left: 120%;
            }

            100% {
                left: 120%;
            }
        }

        /* Hover par thodu vadhare motu thase */
        .btn-shine-fx:hover {
            transform: translateY(-5px) scale(1.05);
            /* Hover par halku motu thase */
            box-shadow: 0 15px 30px rgba(229, 9, 20, 0.6) !important;
        }

        .btn-shine-fx span {
            position: relative;
            z-index: 2;
        }



        .error {
            color: #e50914 !important;
            /* FilmFlix Red color */
            font-size: 13px;
        }

        input.error {
            border: 1px solid #e50914 !important;
        }

        body {
            font-family: 'Inter', sans-serif;
            /* પાછળનું હોમપેજ દેખાય તે માટેનું બેકગ્રાઉન્ડ */
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('Action Movie.jpg');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            overflow: hidden;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('Action Movie.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            /* આ ઉમેરો */
            min-height: 100vh;
            /* height ને બદલે min-height કરો */
            margin: 0;
            display: flex;
            flex-direction: column;
            /* આ સૌથી મહત્વનું છે: આનાથી Header ઉપર રહેશે અને બટન વચ્ચે આવશે */
            justify-content: center;
            align-items: center;
            overflow-x: hidden;
            /* આ ઉમેરો જેથી આડું સ્ક્રોલ ન થાય */
        }

        /* Modal Custom Styling */
        .modal-content {
            border-radius: 24px;
            border: none;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            overflow: hidden;
        }

        .modal-header {
            border: none;
            padding-top: 30px;
            justify-content: center;
            position: relative;
        }

        .modal-title {
            font-weight: 700;
            font-size: 24px;
            color: #1a1a1a;
        }

        .btn-close-custom {
            position: absolute;
            right: 20px;
            top: 20px;
            background: #f0f0f0;
            border: none;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #555;
            transition: 0.3s;
        }

        .btn-close-custom:hover {
            background: #e0e0e0;
            color: #000;
        }

        /* Social Buttons */
        .auth-btn {
            width: 100%;
            padding: 12px;
            margin-bottom: 12px;
            border: 1px solid #e5e5e5;
            border-radius: 12px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            font-weight: 500;
            text-decoration: none;
            color: #333;
            transition: all 0.2s ease;
        }

        .auth-btn:hover {
            background-color: #f9f9f9;
            border-color: #d0d0d0;
            transform: translateY(-2px);
            color: #000;
        }

        /* Guest Button Styling */
        .guest-btn {
            background-color: #f8f9fa;
            border: 1px dashed #bbb;
            color: #666;
        }

        .guest-btn:hover {
            background-color: #fff;
            border-color: #007bff;
            color: #007bff;
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 20px 0;
            color: #bbb;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #eee;
        }

        .divider:not(:empty)::before {
            margin-right: 1em;
        }

        .divider:not(:empty)::after {
            margin-left: 1em;
        }

        /* Input Styling */
        .phone-input-group {
            display: flex;
            align-items: center;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 15px;
            background: #fff;
            transition: 0.3s;
        }

        .phone-input-group:focus-within {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .phone-input-group input {
            border: none;
            outline: none;
            width: 100%;
            margin-left: 10px;
            font-size: 16px;
        }

        .btn-signin {
            width: 100%;
            background: #007bff;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            transition: 0.3s;
        }

        .btn-signin:hover {
            background: #0056b3;
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
        }

        .main-trigger {
            padding: 18px 45px;
            font-size: 20px;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 10px 25px rgba(0, 123, 255, 0.4);
            border: none;
        }
    </style>
</head>

<body>
    <?php include "hhh.php" ?>
    <!-- include '2_Footer.php'; -->


    <button type="button" class="btn main-trigger btn-shine-fx" data-bs-toggle="modal" data-bs-target="#loginModal">
        <span>Get Started</span>
    </button>

    <!-- 
    <button type="button" class="btn btn-light main-trigger" data-bs-toggle="modal" data-bs-target="#loginModal">
        Get Started
    </button> -->

    <!-- <button type="button" class="btn main-trigger btn-shine-fx" data-bs-toggle="modal" data-bs-target="#loginModal">
        <span>Get Started</span>
    </button> -->


    <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">



                <div class="modal-header">
                    <h5 class="modal-title text-danger">Welcome</h5>
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>




                <div class="modal-body p-4">
                    <a href="https://accounts.google.com/" class="auth-btn">
                        <!-- <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_尊_Logo.svg" width="18"> -->
                        <img src="https://fonts.gstatic.com/s/i/productlogos/googleg/v6/24px.svg" width="18" alt="Google Logo" style="margin-right: 8px;">
                        Continue with Google
                    </a>

                    <a href="https://appleid.apple.com/" class="auth-btn">
                        <i class="fa-brands fa-apple" style="font-size: 20px;"></i>
                        Continue with Apple
                    </a>

                    <a href="https://outlook.live.com/" class="auth-btn">
                        <i class="fa-regular fa-envelope" style="font-size: 18px; color: #db4437;"></i>
                        Continue with Email
                    </a>

                    <a href="c.php" class="auth-btn guest-btn">
                        <i class="fa-solid fa-user-secret"></i>
                        Continue as Guest
                    </a>

                    <div class="divider">OR</div>

                    <form action="register.php" method="POST" id="loginForm">
                        <div class="phone-input-group">
                            <img src="https://upload.wikimedia.org/wikipedia/en/4/41/Flag_of_India.svg" width="22" alt="IN">
                            <span class="ms-2 fw-bold text-dark">+91</span>
                            <input type="tel" name="phone" placeholder="Enter mobile number" required>
                        </div>


                        <button type="submit" class="btn btn-success w-100 py-2">Next</button>
                    </form>

                    <p class="text-center mt-4 mb-0" style="font-size: 12px; color: #888;">
                        By continuing, you agree to our <br>
                        <a href="#" class="text-decoration-none fw-bold">Terms of Service</a> &
                        <a href="#" class="text-decoration-none fw-bold">Privacy Policy</a>
                    </p>
                </div>

            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Form ID vapro (#loginForm)
            $("#loginForm").validate({
                rules: {
                    phone: {
                        required: true,
                        digits: true,
                        minlength: 10,
                        maxlength: 10
                    }
                },
                messages: {
                    phone: {
                        required: "Please enter your mobile number",
                        digits: "Only numerical digits are allowed",
                        minlength: "Mobile number must be exactly 10 digits",
                        maxlength: "Mobile number cannot exceed 10 digits"
                    }
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    error.addClass('text-danger mt-1 small');
                    error.insertAfter(element.parent());
                }
            });
        });
    </script>
</body>

</html>