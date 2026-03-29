<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    // Cookie set karein taaki login page par error dikhe
    setcookie("error", "Please login to discover movies!", time() + 5, "/");
    header("Location: login22.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horror Collection - FILMFLIX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #050505;
            color: white;
            padding-top: 80px;
        }

        .section-title {
            border-left: 4px solid #e50914;
            padding-left: 15px;
            margin-bottom: 30px;
            color: white;
        }

        @media (min-width: 992px) {
            .five-cols .col-lg-custom {
                flex: 0 0 20%;
                max-width: 20%;
            }
        }

        .poster-container {
            position: relative;
            transition: transform 0.2s ease;
            cursor: pointer;
            background-color: #1a1a1a;
            border-radius: 8px;
            overflow: hidden;
        }

        .poster-container img {
            width: 100%;
            aspect-ratio: 2/3;
            object-fit: cover;
            display: block;
        }

        .poster-container:hover {
            transform: scale(1.05);
            z-index: 5;
            box-shadow: 0 10px 25px rgba(229, 9, 20, 0.4);
        }

        .poster-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.9));
            padding: 15px;
        }

        .badge-new {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #e50914;
            font-size: 10px;
            padding: 3px 8px;
            font-weight: bold;
            border-radius: 2px;
            z-index: 2;
            color: white;
        }

        .movie-title {
            color: #ffffff !important;
            font-size: 1.1rem;
            margin-top: 10px;
            font-weight: 600;
        }

        /* Smooth filtering transition */
        .movie-item {
            transition: opacity 0.3s ease;
        }
    </style>
</head>

<body style="background:black;">
    <?php include 'hhh.php'; ?>

    <div class="container py-5">
        <div class="row mb-5 justify-content-center">
            <div class="col-md-6">
                <div class="input-group search-wrapper">
                    <span class="input-group-text bg-dark border-secondary text-white">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control bg-dark text-white border-secondary shadow-none"
                        placeholder="Search horror movies..." id="movieSearch">
                    <button class="btn btn-danger px-4" type="button">Search</button>
                </div>
            </div>
        </div>

        <h2 class="section-title text-uppercase">Horror Collection</h2>

        <div class="row g-4 five-cols" id="movieGrid">

            <div class="col-6 col-md-4 col-lg-custom text-center movie-item" data-title="evil dead rise horror 18+">
                <div class="poster-container shadow">
                    <span class="badge-new">18+</span>
                    <img src="hooror.jpg" alt="Evil Dead">
                    <div class="poster-overlay">
                        <a href="theater.php" class="btn btn-sm btn-danger w-100 rounded-pill">Select Session</a>
                    </div>
                </div>
                <div class="movie-title">Evil Dead Rise</div>
            </div>

            <div class="col-6 col-md-4 col-lg-custom text-center movie-item" data-title="the nun II 2 horror ghost">
                <div class="poster-container shadow">
                    <img src="h1.jpg" alt="The Nun">
                    <div class="poster-overlay">
                        <a href="theater.php" class="btn btn-sm btn-danger w-100 rounded-pill">Select Session</a>
                    </div>
                </div>
                <div class="movie-title">The Nun II</div>
            </div>

            <div class="col-6 col-md-4 col-lg-custom text-center movie-item" data-title="insidious red door trending horror">
                <div class="poster-container shadow">
                    <span class="badge-new">TRENDING</span>
                    <img src="h3.jpg" alt="Insidious">
                    <div class="poster-overlay">
                        <a href="theater.php" class="btn btn-sm btn-danger w-100 rounded-pill">Select Session</a>
                    </div>
                </div>
                <div class="movie-title">Insidious: Red Door</div>
            </div>

            <div class="col-6 col-md-4 col-lg-custom text-center movie-item" data-title="talk to me horror supernatural">
                <div class="poster-container shadow">
                    <img src="h4.jpg" alt="Talk To Me">
                    <div class="poster-overlay">
                        <a href="theater.php" class="btn btn-sm btn-danger w-100 rounded-pill">Select Session</a>
                    </div>
                </div>
                <div class="movie-title">Talk To Me</div>
            </div>

            <div class="col-6 col-md-4 col-lg-custom text-center movie-item" data-title="smile hd horror psycho">
                <div class="poster-container shadow">
                    <span class="badge-new">HD</span>
                    <img src="h5.jpg" alt="Smile">
                    <div class="poster-overlay">
                        <a href="theater.php" class="btn btn-sm btn-danger w-100 rounded-pill">Select Session</a>
                    </div>
                </div>
                <div class="movie-title">Smile</div>
            </div>

        </div>
    </div>

    <script>
        document.getElementById('movieSearch').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let items = document.querySelectorAll('.movie-item');

            items.forEach(function(item) {
                let title = item.getAttribute('data-title').toLowerCase();
                if (title.includes(filter)) {
                    item.style.setProperty('display', 'block', 'important');
                } else {
                    item.style.setProperty('display', 'none', 'important');
                }
            });
        });
    </script>

</body>

</html>