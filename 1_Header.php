<!DOCTYPE html>
<html lang="gu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FILMFLIX - Premium Dark Navbar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Navbar Background: Deep Black with Glass Effect */
        .navbar {
            background-color: #000000;
            padding: 10px 0;
            border-bottom: 2px solid #e50914;
            z-index: 10000 !important;
        }

        /* --- Stylish FILMFLIX Logo --- */
        .navbar-brand {
            font-size: 30px;
            letter-spacing: 1px;
            color: #ffffff;
            text-transform: uppercase;

        }

        .navbar-brand span {
            color: #e50914;
            /* 'FLIX' portion in Netflix Red */
            text-shadow: 0 0 10px rgba(229, 9, 20, 0.5);
        }

        .navbar-brand:hover {
            transform: scale(1.05);
            text-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
        }

        /* --- Navigation Links --- */
        .navbar-nav .nav-link {
            color: #cccccc !important;
            font-weight: 500;
            margin: 8px;
            padding: 8px 15px !important;
            border-radius: 5px;
            transition: all 0.3s;
        }

        /* Hover Effect: Text turns white and subtle red glow */
        .navbar-nav .nav-link:hover {
            color: #ffffff !important;
            background-color: rgba(229, 9, 20, 0.1);
            transform: translateY(-5px);
        }

        /* Active Page Link */
        .navbar-nav .nav-link.active {
            color: #e50914 !important;
            border-bottom: 2px solid #e50914;
            border-radius: 0;
        }

        /* Account Button Style */
        .btn-account {
            background-color: #e50914;
            color: white !important;
            font-weight: bold;
            border-radius: 4px;
            padding: 7px 20px !important;
            border: none;
            transition: 0.3s;
        }

        .btn-account:hover {
            background-color: #b20710;
            box-shadow: 0 0 15px rgba(32, 118, 248, 0.4);
            transform: scale(1.05);
        }

        /* Dropdown Styling */
        .dropdown-menu {
            background-color: #ffffff;
            border: 1px solid #333;
            z-index: 9999 !important;
        }

        .dropdown-item {
            color: #000000 !important;
            transition: 0.2s;
        }


        .dropdown-item:hover {
            background-color: #e50914;
            color: white !important;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow">
        <div class="container">

            <a class="navbar-brand fw-bold" href="c.php">
                FILM<span>FLIX</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menu">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link active" href="c.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="gallary.php">Gallary</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>


                </ul>









                <ul class="navbar-nav ms-auto align-items-center">
                    <!-- <li class="nav-item"><a class="nav-link" href="7_Register.php">Register</a></li> -->
                    <!-- <li class="nav-item"><a class="nav-link" href="#.php">Login</a></li> -->

                    <!-- <li class="nav-item ms-2">
                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#userRegistrationModal">
                            Sign Up <i class="fa-solid fa-user-plus"></i>
                        </button>
                    </li> -->
                    <li class="nav-item ms-2">
                        <a href="10_Sign_Up.php" class="btn btn-danger">
                            Sign Up <i class="fa-solid fa-user-plus"></i>
                        </a>
                    </li>
                    <li class="nav-item ms-2">
                        <a href="10_Sign_Up.php" class="btn btn-success">
                            Login
                        </a>
                    </li>
                </ul>
                </li>
                </ul>

            </div>
        </div>
    </nav>

    <div style="margin-top: 100px;"></div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>