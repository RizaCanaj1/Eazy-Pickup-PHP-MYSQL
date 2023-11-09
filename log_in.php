<?php 
include 'includes/header.php';
if($_SESSION['isloggedin'] == true){
    echo "<script>window.location.href='index.php'</script>";
}
if(isset($_POST['login_btn'])){
    $log_in = log_in($_POST['login_btn'],$_POST['email'],$_POST['password']);
    $user = $log_in['user'];
    $pwcheck = $log_in['password'];
    
}
?>
<link href="./assets/css/log_in_register.css" rel="stylesheet">
<div class="container">
    <div class="log_in mt-4 p-4">
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
            <input class="form-control mt-2" type="text" id="email" name="email" placeholder="Email or Phone number" required/>
            <br/>
            <input class="form-control" type="password" id="password" name="password" placeholder="Password" required/>
            <br/>
            <?php if(isset($_POST['login_btn'])): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <p class="p-0 m-0 text-dark">
                    <?php if($user == false) echo"Email doesn't exist"; 
                    else if($user && $pwcheck==false) echo"Password is wrong";?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            <div class="d-flex justify-content-between">
                <div>
                    <label for="keep_me_logged"><p>Keep me logged in</p></label>
                    <input class="ms-4 checkbox"  type="checkbox" id="keep_me_logged" name="keep_me_logged"/>
                </div>
            <a href='#'>Forgot password?</a>
            </div>
            <div class="d-flex justify-content-between">
                <button class="btn btn-secondary" type="submit" name='login_btn'>Log in</button>
                <a href="./register.php">Register</a>
            </div>
        </form>
    </div>
    
</div>

<?php 
include 'includes/footer.php';
?>