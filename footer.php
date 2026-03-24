<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  /* === COMPACT FOOTER === */
  .site-footer {
    background: #000; /* Simple black background for better matching */
    color: #fff;
    padding: 25px 15px 15px; /* Padding bohot kam kar di */
    font-family: 'Roboto', sans-serif;
    border-top: 1px solid #1a1a1a;
  }

  .footer-content {
    display: flex;
    justify-content: space-between;
    align-items: center; /* Sab kuch ek line mein dikhega */
    flex-wrap: wrap;
    max-width: 1200px;
    margin: auto;
  }

  /* Sabhi sections ko chota kiya */
  .footer-logo, .footer-contact, .footer-social {
    flex: 1 1 200px;
    margin: 10px;
  }

  .footer-logo h2 {
    color: #e50914; /* Theme match red */
    font-size: 1.4em; /* Size choti ki */
    margin-bottom: 5px;
  }

  .footer-logo p {
    font-size: 0.85em;
    color: #bbb;
  }

  .footer-contact h3, .footer-social h3 {
    margin-bottom: 10px;
    font-size: 1em; /* Header chota kiya */
    color: #fff;
  }

  .footer-contact p {
    font-size: 0.85em;
    line-height: 1.4;
    margin-bottom: 5px;
    color: #bbb;
  }

  .footer-social a {
    color: #fff;
    font-size: 1.1em; /* Icons chote kiye */
    margin-right: 12px;
    transition: 0.3s;
  }

  .footer-social a:hover {
    color: #e50914;
  }

  .footer-bottom {
    text-align: center;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: 15px;
    margin-top: 20px;
    font-size: 0.8em;
    color: #777;
  }

  /* Background overlay hata diya taaki height kam ho jaye */
</style>

<footer class="site-footer">
  <div class="footer-content">
    <div class="footer-logo">
      <h2>🎬 FILMFLIX</h2>
      <p>Book movies anytime, anywhere.</p>
    </div>

    <div class="footer-contact">
      <h3>Contact</h3>
      <p><i class="fas fa-phone-alt"></i> +91 12345 67890</p>
      <p><i class="fas fa-envelope"></i> help@filmflix.com</p>
    </div>

    <div class="footer-social">
      <h3>Follow Us</h3>
      <a href="#"><i class="fab fa-facebook-f"></i></a>
      <a href="#"><i class="fab fa-instagram"></i></a>
      <a href="#"><i class="fab fa-youtube"></i></a>
    </div>
  </div>

  <div class="footer-bottom">
    <p>&copy; <?php echo date("Y"); ?> FILMFLIX. All rights reserved.</p>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>