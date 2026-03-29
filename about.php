<?php
include 'config.php';

// છેલ્લે અપડેટ થયેલું કન્ટેન્ટ બતાવવા માટે ORDER BY ID DESC વાપર્યું છે
$res = mysqli_query($con, "SELECT * FROM about_content ORDER BY id DESC LIMIT 1");
$data = mysqli_fetch_assoc($res);

// જો ડેટાબેઝ ખાલી હોય તો એરર ન આવે તે માટે ડિફોલ્ટ વેલ્યુ
if (!$data) {
    $data = ['hero_subtitle' => '', 'main_image' => '', 'about_welcome_title' => '', 'about_main_title' => '', 'about_full_desc' => '', 'user_exp_val' => 0, 'secure_pay_val' => 0, 'stats_movies' => '0', 'stats_users' => '0'];
}

// બધી જ Features અને Info Cards ફેચ કરવા માટે (LIMIT કાઢી નાખ્યું છે)
$features = mysqli_query($con, "SELECT * FROM about_cards WHERE card_type='feature'");
$infos = mysqli_query($con, "SELECT * FROM about_cards WHERE card_type='info'");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>About Us - FILMFLIX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: 'Inter', sans-serif;
            padding-top: 75px;
        }

        .hero-section {
            padding: 100px 0;
            background: radial-gradient(circle at center, #1a0202 0%, #000000 100%);
            text-align: center;
        }

        .highlight {
            color: #e50914;
            text-shadow: 0 0 20px rgba(229, 9, 20, 0.4);
        }

        .about-img {
            width: 100%;
            border-radius: 15px;
            border: 2px solid #e50914;
            box-shadow: 0 0 30px rgba(229, 9, 20, 0.5);
        }

        .feature-card,
        .info-box {
            background: #1a1a1a;
            border: 1px solid #333;
            padding: 30px;
            border-radius: 20px;
            transition: 0.3s;
            height: 100%;
            text-align: center;
        }

        .feature-card:hover,
        .info-box:hover {
            border-color: #e50914;
            transform: translateY(-10px);
        }

        .icon-box {
            width: 60px;
            height: 60px;
            background: rgba(229, 9, 20, 0.1);
            color: #e50914;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            font-size: 24px;
            margin: 0 auto 20px;
        }

        .stats-section {
            background-color: #050505;
            padding: 80px 0;
            border-top: 1px solid #111;
        }

        .stats-number {
            font-size: 3.5rem;
            font-weight: 800;
            color: #e50914;
            display: block;
        }

        .progress {
            background-color: #333;
            height: 8px;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <section class="hero-section">
        <div class="container">
            <h1 class="display-4 fw-bold text-light">Welcome to <span class="highlight">FILMFLIX</span></h1>
            <p class="lead mt-3 text-light"><?php echo $data['hero_subtitle']; ?></p>
        </div>
    </section><br>

    <section class="bg-black py-5">
        <div class="container">
            <div class="card bg-dark text-white border-secondary shadow-lg rounded-4 p-4">
                <div class="row align-items-center gx-lg-5">
                    <div class="col-md-5 mb-4 mb-md-0">
                        <img src="<?php echo $data['main_image']; ?>" class="about-img">
                    </div>
                    <div class="col-md-7">
                        <h6 class="text-danger fw-bold text-uppercase"><?php echo $data['about_welcome_title']; ?></h6>
                        <h2 class="display-5 fw-bold mb-4"><?php echo $data['about_main_title']; ?></h2>
                        <p class="text-secondary mb-4"><?php echo $data['about_full_desc']; ?></p>

                        <small>User Experience (<?php echo $data['user_exp_val']; ?>%)</small>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-danger" style="width: <?php echo $data['user_exp_val']; ?>%"></div>
                        </div>

                        <small>Secure Payment (<?php echo $data['secure_pay_val']; ?>%)</small>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-success" style="width: <?php echo $data['secure_pay_val']; ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><br>

    <section class="py-5 bg-black">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-light">Why Choose FILMFLIX?</h2>
            </div>
            <div class="row g-4">
                <?php while ($row = mysqli_fetch_assoc($infos)) { ?>
                    <div class="col-md-4">
                        <div class="info-box">
                            <h4 class="fw-bold text-light"><?php echo $row['icon_class']; ?> <?php echo $row['title']; ?></h4>
                            <p class="text-secondary"><?php echo $row['description']; ?></p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <section class="py-5 bg-black">
        <div class="container">
            <div class="row g-4 text-center">
                <?php while ($row = mysqli_fetch_assoc($features)) { ?>
                    <div class="col-md-4">
                        <div class="feature-card">
                            <div class="icon-box"><i class="<?php echo $row['icon_class']; ?>"></i></div>
                            <h4 class="text-light"><?php echo $row['title']; ?></h4>
                            <p class="text-secondary"><?php echo $row['description']; ?></p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section><br>

    <section class="stats-section text-center">
        <div class="container">
            <div class="row">
                <div class="col-md-4"><span class="stats-number"><?php echo $data['stats_movies']; ?></span>
                    <p class="text-light">Movies Available</p>
                </div>
                <div class="col-md-4"><span class="stats-number"><?php echo $data['stats_users']; ?></span>
                    <p class="text-light">Active Users</p>
                </div>
                <div class="col-md-4"><span class="stats-number">24/7</span>
                    <p class="text-light">Customer Support</p>
                </div>
            </div>
        </div>
    </section>
    <br>
    <?php include 'footer.php'; ?>
</body>

</html>