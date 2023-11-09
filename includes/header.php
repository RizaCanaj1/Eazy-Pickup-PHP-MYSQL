<?php
include 'includes/CRUD/main.php';
session_start();
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$current_day = date('Y-m-d');
$current_month = date('Y-m');
$current_year = date('Y');
if(!isset($_SESSION['isloggedin'])){
    $_SESSION['id'] = null;
    $_SESSION['email'] = null;
    $_SESSION['isloggedin'] = false;
}
else{
    $id=$_SESSION['id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="./assets/css/header.css" rel="stylesheet">
    <title>Eazy-Pick</title>
</head>
<body>
<nav class="navbar bg-dark navbar-expand-lg bg-body-tertiary" data-bs-theme="dark">
    <div class="container container-fluid">
        <a class="navbar-brand" href="index.php">Eazy-Pick</a>
        <div class="d-flex align-items-center gap-3">
            <form action='shop.php' class="d-flex searchbar" role="search">
            <input name="search" class="form-control me-2 searcher" type="search" placeholder="Search" aria-label="Search">
            <button type="submit"><i class="fa-solid fa-magnifying-glass" style="color: #e3e7ed;"></i></button>
            </form>
            <i class="fa-solid fa-filter" style="color: #e3e7ed;"></i>
        </div>
        
        <div>
            <div class="collapse navbar-collapse" id="navbarText">
                
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if($_SESSION['isloggedin']):?>
                <?php if(!strpos( $url, 'chat.php')):?>
                <li class="nav-item">
                    <a class="nav-link" href="chat.php"><strong>Chat</strong></a>
                </li>
                <?php endif ?>
                <?php if(!strpos( $url, 'profile.php')):?>
                <li class="nav-item">
                    <a class="nav-link" <?php echo"href='profile.php?id=$id'"?>><strong>Profile</strong></a>
                </li>
                <?php endif ?>
                <li class="nav-item">
                    <a class="nav-link" href="log_out.php"><strong>Log out</strong></a>
                </li>
                <?php else :?>
                <?php if(!strpos( $url, 'log_in.php')):?>
                <li class="nav-item">
                    <a class="nav-link" href="log_in.php"><strong>Log in</strong></a>
                </li>
                <?php endif ?>
                <?php endif ?>
            </ul>
            </div>
        </div>
    </div>
</nav>
                
<form class="container filter" action='shop.php'>
    <div class='form-group categories-filter d-flex justify-content-center align-items-center'>
        <select class="form-control text-center" name="categories" id="categories">
            <option value="">----Chose-Categories----</option>
            <option value="clothes">Clothes</option>
            <option value="electronics">Electronics</option>
            <option value="furniture">Furniture</option>
            <option value="automobiles">Automobiles</option>
            <option value="others">Others</option>
        </select>
    </div>
    <div class="d-flex justify-content-center align-items-center">
        <div class="price ">
            <div class="s-from d-flex gap-2 form-group">
                <p class="mt-3">Starting from:</p>
                <input class="starts-from" name='starts-from' type="range" min="0" max="5000" value="0" id='start'>
                <input class="s-value form-control mt-3" type="number" value='0' min="0" max="5000" id='start'>
            </div>
            <br/>
            <div class="e-at d-flex gap-2 form-group">
                <p>Ends at:</p>
                <input class="ends-at" name='ends-at' type="range" min="0" max="5000" value="0" id='end' >
                <input class="e-value form-control" type="number" value='0' min="0" max="5000" id='end'>
            </div>
        </div>
    </div>
    <div class='form-group order-filter  d-flex justify-content-center align-items-center'>
        <select class="form-control text-center" name="order" id="order">
            <option value="">----Order-by----</option>
            <option value="latest">Latest</option>
            <option value="oldest">Oldest</option>
            <option value="cheapest">Cheapest</option>
            <option value="most_expensive">Most expensive</option>
        </select>
    </div>
    <input class='d-none searcher' name="search" class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
    <button class='form-group form-control apply-btn mt-5'>Apply</button>
</form>