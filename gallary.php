<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FILMFLIX - MOVIE GALLERY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            background-image: url('back.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            padding-top: 100px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: white;
        }

        .search-wrapper-main {
            margin-bottom: 40px;
        }

        .search-input {
            border-radius: 50px 0 0 50px !important;
            padding: 12px 25px !important;
            border: 2px solid #e50914 !important;
            background: rgba(0, 0, 0, 0.8) !important;
            color: white !important;
        }

        .search-btn {
            border-radius: 0 50px 50px 0 !important;
            background-color: #e50914 !important;
            border: 2px solid #e50914 !important;
            color: white !important;
            padding: 0 30px !important;
            font-weight: bold;
        }

        .cat-img {
            height: 250px;
            object-fit: cover;
            width: 100%;
            transition: 0.5s;
        }

        .card {
            background: #000000 !important;
            border: 1px solid #222 !important;
            transition: 0.4s;
            overflow: hidden;
            height: 100%;
        }

        .card:hover {
            transform: scale(1.05);
            border-color: #e50914 !important;
        }

        .card-title {
            color: white;
            font-weight: 700;
        }

        .hr-style {
            width: 80px;
            height: 3px;
            background-color: #e50914;
            opacity: 1;
            margin: 20px auto;
            border: none;
        }

        .movie-item {
            transition: all 0.4s ease;
        }
    </style>
</head>

<body>

    <?php include 'header.php'; ?>

    <div class="container">

        <div class="text-center mb-5">
            <h2 class="fw-bold text-light">MOVIE GALLERY</h2>
            <p class="text-light">Explore categories and register for updates</p>
            <hr class="hr-style">
        </div>

        <div class="row justify-content-center search-wrapper-main">
            <div class="col-md-6">
                <div class="input-group shadow-lg">
                    <input type="text" class="form-control search-input shadow-none"
                        placeholder="Type to filter movies (horror, action, south...)" id="movieSearch">
                    <button class="btn search-btn" type="button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5" id="movieGrid">

            <?php
            // Database mathi data fetch karva mate
            $query = "SELECT * FROM gallery ORDER BY id DESC";
            $result = mysqli_query($con, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Search mate tags ane title combine kariye
                    $tags = strtolower($row['category_tag'] . " " . $row['title']);
            ?>
                    <div class="col-md-4 movie-item" data-title="<?php echo $tags; ?>">
                        <div class="card shadow rounded-4">
                            <img src="<?php echo $row['image_url']; ?>" class="card-img-top cat-img" alt="<?php echo $row['title']; ?>">
                            <div class="card-body p-4 text-center d-flex flex-column">
                                <h5 class="card-title text-light"><?php echo $row['title']; ?></h5>
                                <p class="text-light small"><?php echo $row['description']; ?></p>
                                <a href="<?php echo $row['link_url']; ?>" class="btn btn-danger w-100 py-2 mt-auto rounded-pill">Discover Now</a>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<div class='text-center w-100'><h4 class='text-light'>No movies found. Add some from Admin Panel!</h4></div>";
            }
            ?>

        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        // Filtering Logic
        document.getElementById('movieSearch').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let items = document.querySelectorAll('.movie-item');

            items.forEach(function(item) {
                let text = item.getAttribute('data-title');
                if (text.includes(filter)) {
                    item.style.display = "block";
                    setTimeout(() => item.style.opacity = "1", 10);
                } else {
                    item.style.opacity = "0";
                    setTimeout(() => item.style.display = "none", 400);
                }
            });
        });
    </script>

</body>

</html>