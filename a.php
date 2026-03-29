<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FILMFLIX - Full Screen Experience</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Removes spacing for true full screen and hides scrollbar if needed */
        body, html { margin: 0; padding: 0; height: 100%; }

        /* Full Screen Carousel Logic */
        .carousel-item div { 
            height: 100vh; 
            min-height: 500px;
            background-size: cover !important;
            background-position: center !important;
        }

        /* Overlay to make text pop */
        .hero-overlay {
            background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.7));
            width: 100%;
            height: 100%;
        }

        /* Navbar adjustment for transparency over carousel */
        .fixed-top { background: rgba(0,0,0,0.4) !important; backdrop-filter: blur(5px); }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div id="homeCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="2"></button>
            <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="3"></button>
        </div>

        <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="4000">
                <div class="d-flex flex-column justify-content-center align-items-center text-white text-center"
                     style="background: url('t.jpg');">
                    <div class="hero-overlay d-flex flex-column justify-content-center align-items-center p-3">
                        <h1 class="display-1 fw-bold">Welcome to FILMFLIX</h1>
                        <p class="lead fs-3">Your ultimate destination for cinematic excellence.</p>
                        <button class="btn btn-primary btn-lg rounded-pill px-5 mt-3">Watch Trailer</button>
                    </div>
                </div>
            </div>

            <div class="carousel-item" data-bs-interval="4000">
                <div class="d-flex flex-column justify-content-center align-items-center text-white text-center"
                     style="background: url('im.jpg');">
                    <div class="hero-overlay d-flex flex-column justify-content-center align-items-center p-3">
                        <h1 class="display-1 fw-bold text-uppercase">Silent Spectacle</h1>
                        <p class="lead fs-3">Experience the high-stakes thriller of the year.</p>
                        <button class="btn btn-outline-light btn-lg rounded-pill px-5 mt-3">Get Tickets</button>
                    </div>
                </div>
            </div>

            <div class="carousel-item" data-bs-interval="4000">
                <div class="d-flex flex-column justify-content-center align-items-center text-white text-center"
                     style="background: url('s.jpg');">
                    <div class="hero-overlay d-flex flex-column justify-content-center align-items-center p-3">
                        <h1 class="display-1 fw-bold">South Action Hits</h1>
                        <p class="lead fs-3">Witness legendary battles and heroic journeys.</p>
                        <button class="btn btn-warning btn-lg rounded-pill px-5 mt-3">Explore Now</button>
                    </div>
                </div>
            </div>

            <div class="carousel-item" data-bs-interval="4000">
                <div class="d-flex flex-column justify-content-center align-items-center text-white text-center"
                     style="background: url('i.jpg');">
                    <div class="hero-overlay d-flex flex-column justify-content-center align-items-center p-3">
                        <h1 class="display-1 fw-bold">Horror Unleashed</h1>
                        <p class="lead fs-3">Do you dare to enter the Nightmare Forest?</p>
                        <button class="btn btn-danger btn-lg rounded-pill px-5 mt-3">Watch If You Dare</button>
                    </div>
                </div>
            </div>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#homeCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon shadow"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#homeCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon shadow"></span>
        </button>
    </div>
     <section id="plans" class="py-5 bg-dark">
        <div class="container text-center">
            <h2 class="fw-bold mb-5">Membership Plans</h2>
            <div class="row g-4 justify-content-center">
                <div class="col-md-4">
                    <div class="card pricing-card text-white p-4 h-100">
                        <h4>Standard</h4>
                        <h2 class="text-primary my-3">₹299<small class="fs-6">/mo</small></h2>
                        <ul class="list-unstyled my-4 text-secondary">
                            <li><i class="bi bi-check-lg me-2"></i>1080p Full HD</li>
                            <li><i class="bi bi-check-lg me-2"></i>2 Devices</li>
                            <li><i class="bi bi-check-lg me-2"></i>Ad-free</li>
                        </ul>
                        <button class="btn btn-outline-primary mt-auto">Subscribe</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card pricing-card text-white p-4 h-100 border-primary shadow-lg" style="transform: scale(1.05);">
                        <div class="badge bg-primary mb-2 align-self-center">POPULAR</div>
                        <h4>Premium</h4>
                        <h2 class="text-primary my-3">₹649<small class="fs-6">/mo</small></h2>
                        <ul class="list-unstyled my-4 text-secondary">
                            <li><i class="bi bi-check-lg me-2"></i>4K + HDR</li>
                            <li><i class="bi bi-check-lg me-2"></i>4 Devices</li>
                            <li><i class="bi bi-check-lg me-2"></i>Offline Downloads</li>
                        </ul>
                        <button class="btn btn-primary mt-auto">Go Premium</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-5 bg-black border-top border-secondary">
        <div class="container text-center text-md-start">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h4 class="text-primary fw-bold">FILMFLIX</h4>
                    <p class="text-secondary">Stream your favorite movies and shows in the best quality possible.</p>
                </div>
                <div class="col-md-4 mb-4 text-center">
                    <h6 class="text-white">Follow Us</h6>
                    <div class="fs-3 d-flex justify-content-center gap-3 mt-3">
                        <i class="bi bi-facebook text-secondary"></i>
                        <i class="bi bi-instagram text-secondary"></i>
                        <i class="bi bi-youtube text-secondary"></i>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <h6 class="text-white">Legal</h6>
                    <nav class="nav flex-column small">
                        <a class="nav-link text-secondary p-0 mb-1" href="#">Privacy Policy</a>
                        <a class="nav-link text-secondary p-0 mb-1" href="#">Terms of Use</a>
                        <a class="nav-link text-secondary p-0 mb-1" href="#">Help Center</a>
                    </nav>
                </div>
            </div>
            <hr class="bg-secondary">
            <p class="text-center text-secondary small mb-0">&copy; 2026 FILMFLIX. All rights reserved.</p>
        </div>
    </footer>
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>