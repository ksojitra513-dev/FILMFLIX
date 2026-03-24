<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FILMFLIX - MOVIE GALLARY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <style>
        body {
            background-image: url('back.jpg');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            padding: 100px;
            font-family: 'segoe UI', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .section-header {
            margin-top: 30px; /* Header ke baad extra space */
            margin-bottom: 50px;
}



        .search-container {
            max-width: 700px;
            margin: 0 auto 30px auto;
            /* Margin thoda kam kiya button ke liye */
        }

        .search-input {
            border-radius: 50px 0 0 50px !important;
            padding: 12px 25px !important;
            border: 2px solid #e50914 !important;
            background: #f3ecec !important;
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

        /* Movie Card Styles */
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
        }

        .card:hover {
            transform: scale(1.05);
            border-color: #e50914 !important;
        }

        .card:hover .cat-img {
            transform: scale(1.1);
        }

        .card-title {
            color: white;
            font-weight: 700;
        }

        .text-muted {
            color: #aaa !important;
        }

        .hr-style {
            width: 80px;
            height: 3px;
            background-color: #e50914;
            opacity: 1;
            margin: 20px auto;
            border: none;
        }

        /* Custom Booking Button Style */
        .booking-section {
            background: rgba(229, 9, 20, 0.1);
            border: 1px dashed #e50914;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 50px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5 mb-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-uppercase text-white">ALL THEATRES</h2>
            <div class="hr-style"></div>
        </div>
        
        <div class="row g-4">
            
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow rounded-4">
                    <img src="i.jpg" class="card-img-top cat-img" alt="Connplex Cinemas">
                    <div class="card-body p-4 text-center d-flex flex-column">
                        <h5 class="fw-bold card-title text-white">Connplex Cinemas</h5>
                        <p class="text-light small">2nd Ring Rd, near Valupani restaurant, Rajkot. On-site services available.</p>
                        <a href="time.php" class="btn btn-danger w-100 py-2 mt-auto">Show Time</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow rounded-4">
                    <img src="south.jpg" class="card-img-top cat-img" alt="Galaxy Cinema">
                    <div class="card-body p-4 text-center d-flex flex-column">
                        <h5 class="fw-bold card-title text-white">Galaxy Cinema</h5>
                        <p class="text-light small">Race Course Ring Road, Next To Police Commissioner Office. Old school cinema.</p>
                        <a href="time.php" class="btn btn-danger w-100 py-2 mt-auto">Show Time</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow rounded-4">
                    <img src="https://m.media-amazon.com/images/I/C1qmG3fnRpS.png" class="card-img-top cat-img" alt="R World INOX">
                    <div class="card-body p-4 text-center d-flex flex-column">
                        <h5 class="fw-bold card-title text-white">R World INOX</h5>
                        <p class="text-light small">Old Dharam Cinema Building, Kasturba Rd. Premium movie experience.</p>
                        <a href="time.php" class="btn btn-danger w-100 py-2 mt-auto">Show Time</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow rounded-4">
                    <img src="https://bingeddata.s3.amazonaws.com/uploads/2021/06/Best-50-Hollywood-Movies-For-You-To-Stream-On-SonyLIV-768x576.jpg" class="card-img-top cat-img" alt="Cosmoplex">
                    <div class="card-body p-4 text-center d-flex flex-column">
                        <h5 class="fw-bold card-title text-white">Cosmoplex</h5>
                        <p class="text-light small">Kalavad Rd, Rajkot. Multiscreen cinema with games & a cafe.</p>
                        <a href="time.php" class="btn btn-danger w-100 py-2 mt-auto">Show Time</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow rounded-4">
                    <img src="https://filmfare.wwmindia.com/content/2021/aug/bestbollywoodromanticmovies101628225139.jpg" class="card-img-top cat-img" alt="Cinepolis">
                    <div class="card-body p-4 text-center d-flex flex-column">
                        <h5 class="fw-bold card-title text-white">Cinepolis</h5>
                        <p class="text-light small">Vandana Heritage, Gondal Rd. Upscale digital movie theater.</p>
                        <a href="time.php" class="btn btn-danger w-100 py-2 mt-auto">Show Time</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow rounded-4">
                    <img src="https://pic-bstarstatic.akamaized.net/ugc/1458a512a5ee4a661473cbcf9e26c246.jpg" class="card-img-top cat-img" alt="Movie Time Cinemas">
                    <div class="card-body p-4 text-center d-flex flex-column">
                        <h5 class="fw-bold card-title text-white">Movie Time Cinemas</h5>
                        <p class="text-light small">2nd Floor, Crystal Mall Rajkot, Kalavad Rd. Central location.</p>
                        <a href="time.php" class="btn btn-danger w-100 py-2 mt-auto">Show Time</a>
                    </div>
                </div>
            </div>

        </div> 
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php include 'footer.php'; ?>
   </body>
</html>