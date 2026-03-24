<?php
include 'config.php'; // Database connection file

// Jyare "Add Movie" button par click thai tyre
if (isset($_POST['add_movie'])) {
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $tags = mysqli_real_escape_string($con, $_POST['tags']);
    $desc = mysqli_real_escape_string($con, $_POST['desc']);
    $link = mysqli_real_escape_string($con, $_POST['link']);

    // 1. Photo Upload Logic
    $target_dir = "uploads/";
    // Jo folder na hoy to banavi deve
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $image_name = time() . "_" . basename($_FILES["image"]["name"]); // Unique name mate time() vapryu
    $target_file = $target_dir . $image_name;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // 2. Database ma Entry
        $sql = "INSERT INTO gallery (title, category_tag, description, image_url, link_url) 
                VALUES ('$title', '$tags', '$desc', '$target_file', '$link')";

        if (mysqli_query($con, $sql)) {
            echo "<script>alert('Movie Added Successfully!'); window.location='admin.php';</script>";
        } else {
            echo "Error: " . mysqli_error($con);
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

// Movie Delete Logic
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $res = mysqli_query($con, "SELECT image_url FROM gallery WHERE id=$id");
    $row = mysqli_fetch_assoc($res);
    if ($row) {
        unlink($row['image_url']);
    } // Folder mathi photo delete karva
    mysqli_query($con, "DELETE FROM gallery WHERE id=$id");
    header("Location: g22.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin - FilmFlix Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #1c1c1c;
            color: white;
            padding: 40px 0;
        }

        .card-admin {
            background: #2b2b2b;
            border: none;
            border-radius: 15px;
            padding: 25px;
        }

        .form-control {
            background: #3d3d3d;
            border: 1px solid #555;
            color: white;
        }

        .form-control:focus {
            background: #444;
            color: white;
            border-color: #e50914;
            box-shadow: none;
        }

        .btn-danger {
            background: #e50914;
            border: none;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="text-center mb-4">🎥 FilmFlix Admin Panel</h2>

                <div class="card-admin shadow-lg mb-5">
                    <h4 class="mb-3">Add New Movie</h4>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label>Movie Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Inception" required>
                        </div>
                        <div class="mb-3">
                            <label>Search Tags (Comma separated)</label>
                            <input type="text" name="tags" class="form-control" placeholder="e.g. sci-fi, action, hollywood" required>
                        </div>
                        <div class="mb-3">
                            <label>Short Description</label>
                            <textarea name="desc" class="form-control" rows="3" placeholder="Brief about the movie..."></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Discover Page Link</label>
                                <input type="text" name="link" class="form-control" value="discover.php">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Movie Poster (Image)</label>
                                <input type="file" name="image" class="form-control" accept="image/*" required>
                            </div>
                        </div>
                        <button type="submit" name="add_movie" class="btn btn-danger w-100 fw-bold">UPLOAD MOVIE</button>
                    </form>
                </div>

                <div class="card-admin">
                    <h4 class="mb-3">Currently in Gallery</h4>
                    <table class="table table-dark table-hover">
                        <thead>
                            <tr>
                                <th>Poster</th>
                                <th>Title</th>
                                <th>Tags</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $res = mysqli_query($con, "SELECT * FROM gallery ORDER BY id DESC");
                            while ($row = mysqli_fetch_assoc($res)) {
                            ?>
                                <tr>
                                    <td><img src="<?php echo $row['image_url']; ?>" width="50" style="border-radius: 5px;"></td>
                                    <td><?php echo $row['title']; ?></td>
                                    <td><small class="text-secondary"><?php echo $row['category_tag']; ?></small></td>
                                    <td>
                                        <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this movie?')">Delete</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>

</html>