<?php
$type = $_GET['type'] ?? 'action';

$cat_map = [
    'action'      => ['title' => 'Action Movies',      'table' => 'action_movies'],
    'bollywood'   => ['title' => 'Bollywood Movies',   'table' => 'bollywood_movies'],
    'hollywood'   => ['title' => 'Hollywood Movies',   'table' => 'hollywood_movies'],
    'south'       => ['title' => 'South Indian',       'table' => 'south_movies'],
    'horror'      => ['title' => 'Horror Movies',      'table' => 'horror_movies'],
    'comedy'      => ['title' => 'Comedy Movies',      'table' => 'comedy_movies'],
    'romantic'    => ['title' => 'Romantic Movies',    'table' => 'romantic_movies'],
    'scifi'       => ['title' => 'Sci-Fi Movies',      'table' => 'scifi_movies'],
    'animated'    => ['title' => 'Animated Movies',    'table' => 'animated_movies'],
    'documentary' => ['title' => 'Documentary Movies', 'table' => 'documentary_movies'],
    'classic'     => ['title' => 'Classic Movies',     'table' => 'classic_movies'],
    'thriller'    => ['title' => 'Thriller Movies',    'table' => 'thriller_movies']
];

if (!isset($cat_map[$type])) {
    $type = 'action';
}

$page_title = $cat_map[$type]['title'];
$table_name = $cat_map[$type]['table'];

include 'category_base.php';
?>
