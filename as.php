<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FILMFLIX - Full Screen Experience</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        /* Base Setup */
        body, html { margin: 0; padding: 0; background-color: #000; color: white; overflow-x: hidden; }

        /* Full Screen Carousel Logic */
        .carousel-item div { 
            height: 100vh; 
            min-height: 500px;
            background-size: cover !important;
            background-position: center !important;
        }

        /* Overlay to make text pop */
        .hero-overlay {
            background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.9));
            width: 100%;
            height: 100%;
        }

        /* Navbar adjustment */
        .fixed-top { 
            background: rgba(0,0,0,0.6) !important; 
            backdrop-filter: blur(10px); 
            transition: 0.4s;
        }

        /* Movie Card Hover Effect */
        .movie-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            border: none;
            background: transparent;
        }
        .movie-card:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0,0,0,0.5);
        }
        .movie-card img {
            border-radius: 10px;
            object-fit: cover;
        }

        /* Genre Pill Style */
        .genre-pill {
            white-space: nowrap;
            padding: 8px 25px;
            border-radius: 50px;
            border: 1px solid #333;
            color: #ccc;
            text-decoration: none;
            transition: 0.3s;
        }
        .genre-pill:hover {
            background: #fff;
            color: #000;
        }

        /* Pricing Card */
        .pricing-card {
            background: #111;
            border: 1px solid #222;
            transition: 0.3s;
        }
        .pricing-card:hover {
            border-color: #0d6efd;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top py-3">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3 text-primary" href="#">FILMFLIX</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto fw-semibold">
                    <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#trending">Trending</a></li>
                    <li class="nav-item"><a class="nav-link" href="#plans">Plans</a></li>
                    <li class="nav-item ms-lg-3"><a class="btn btn-primary rounded-pill px-4" href="#">Login</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div id="homeCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="2"></button>
        </div>

        <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="4000">
                <div class="d-flex flex-column justify-content-center align-items-center text-white text-center"
                     style="background: url('https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=1925&auto=format&fit=crop');">
                    <div class="hero-overlay d-flex flex-column justify-content-center align-items-center p-3">
                        <h1 class="display-1 fw-bold">Welcome to FILMFLIX</h1>
                        <p class="lead fs-3">Your ultimate destination for cinematic excellence.</p>
                        <button class="btn btn-primary btn-lg rounded-pill px-5 mt-3 shadow">Watch Trailer</button>
                    </div>
                </div>
            </div>

            <div class="carousel-item" data-bs-interval="4000">
                <div class="d-flex flex-column justify-content-center align-items-center text-white text-center"
                     style="background: url('https://images.unsplash.com/photo-1626814026160-2237a95fc5a0?q=80&w=2070&auto=format&fit=crop');">
                    <div class="hero-overlay d-flex flex-column justify-content-center align-items-center p-3">
                        <h1 class="display-1 fw-bold text-uppercase">Silent Spectacle</h1>
                        <p class="lead fs-3">Experience the high-stakes thriller of the year.</p>
                        <button class="btn btn-outline-light btn-lg rounded-pill px-5 mt-3">Get Tickets</button>
                    </div>
                </div>
            </div>

            <div class="carousel-item" data-bs-interval="4000">
                <div class="d-flex flex-column justify-content-center align-items-center text-white text-center"
                     style="background: url('https://images.unsplash.com/photo-1485846234645-a62644f84728?q=80&w=2059&auto=format&fit=crop');">
                    <div class="hero-overlay d-flex flex-column justify-content-center align-items-center p-3">
                        <h1 class="display-1 fw-bold">South Action Hits</h1>
                        <p class="lead fs-3">Witness legendary battles and heroic journeys.</p>
                        <button class="btn btn-warning btn-lg rounded-pill px-5 mt-3">Explore Now</button>
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

    <div class="container py-5">
        <div class="d-flex overflow-auto pb-2 gap-3 no-scrollbar" style="scrollbar-width: none;">
            <a href="#" class="genre-pill">Action</a>
            <a href="#" class="genre-pill">Comedy</a>
            <a href="#" class="genre-pill">Horror</a>
            <a href="#" class="genre-pill">Sci-Fi</a>
            <a href="#" class="genre-pill">Drama</a>
            <a href="#" class="genre-pill">Thriller</a>
            <a href="#" class="genre-pill">Romance</a>
            <a href="#" class="genre-pill">Animation</a>
        </div>
    </div>

    <section id="trending" class="pb-5">
        <div class="container">
            <h3 class="mb-4 fw-bold">Trending Now</h3>
            <div class="row g-4">
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card movie-card">
                        <img src="https://image.tmdb.org/t/p/w500/8Gxv2mYgiFAfy9R6t99vR97br13.jpg" class="card-img-top" alt="Movie">
                        <div class="card-body px-0 pt-2">
                            <h6 class="card-title mb-0 small">The Dark Knight</h6>
                            <p class="text-secondary small">2008 • Action</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card movie-card">
                        <img src="https://image.tmdb.org/t/p/w500/uKVLTq1zIPoQQsl98O9tvi4p6Yv.jpg" class="card-img-top" alt="Movie">
                        <div class="card-body px-0 pt-2">
                            <h6 class="card-title mb-0 small">Interstellar</h6>
                            <p class="text-secondary small">2014 • Sci-Fi</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card movie-card">
                        <img src="https://image.tmdb.org/t/p/w500/qJ2tW6WMUDp9QmSbmrQvC2q7Uc2.jpg" class="card-img-top" alt="Movie">
                        <div class="card-body px-0 pt-2">
                            <h6 class="card-title mb-0 small">Joker</h6>
                            <p class="text-secondary small">2019 • Drama</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card movie-card">
                        <img src="https://image.tmdb.org/t/p/w500/iuS7U663TTIub59vYp9Y9Iu96rZ.jpg" class="card-img-top" alt="Movie">
                        <div class="card-body px-0 pt-2">
                            <h6 class="card-title mb-0 small">Tenet</h6>
                            <p class="text-secondary small">2020 • Sci-Fi</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card movie-card">
                        <img src="https://image.tmdb.org/t/p/w500/bVq65huS86HDXsB17UNJqX9KyKy.jpg" class="card-img-top" alt="Movie">
                        <div class="card-body px-0 pt-2">
                            <h6 class="card-title mb-0 small">The Batman</h6>
                            <p class="text-secondary small">2022 • Action</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card movie-card">
                        <img src="https://image.tmdb.org/t/p/w500/7WsyChvYDf3tIcy1aU2YpSvmZWx.jpg" class="card-img-top" alt="Movie">
                        <div class="card-body px-0 pt-2">
                            <h6 class="card-title mb-0 small">Inception</h6>
                            <p class="text-secondary small">2010 • Thriller</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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