<?php 
include 'includes/header.php';
if($_SESSION['isloggedin'] == true){
    header('Location: index.php');
}
if(isset($_POST['register_btn'])){
    $name=$_POST['name'];
    $surname=$_POST['sname'];
    $email=$_POST['email'];
    $password=$_POST['password'];
    $password_again=$_POST['again_password'];
    $number=$_POST['number'];
    if(empty($_POST['number'])){
        $number=NULL;
    }
    $password_match=($password==$password_again);
    $stm = $pdo->prepare('SELECT * FROM  `user` WHERE `email` = ?');
    $stm->execute([$email]);
    $user = $stm->fetch(PDO::FETCH_ASSOC);
    $stm = $pdo->prepare('SELECT * FROM  `user` WHERE `phone_number` = ?');
    $stm->execute([$number]);
    $number_check = $stm->fetch(PDO::FETCH_ASSOC);
    if(!$user && !$number_check && $password_match){
        $stm = $pdo->prepare('INSERT INTO `user` (`firstname`, `lastname`, `email`, `password`,`phone_number`) VALUES (?, ?, ?, ? ,?)');
        
        if($stm->execute([$name, $surname, $email, password_hash($password, PASSWORD_BCRYPT ),$number])) {
            if(isset($_POST['keep_me_logged'])){
                $id = $pdo->lastInsertId();
                $_SESSION['id'] = $id;
                $_SESSION['email'] = $email;
                $_SESSION['isloggedin'] = true;
                header('Location: profile.php?id='.$id);
            }
            else{
                header('Location: log_in.php');
            }
            
        }
       
    }
    
    /*
    $stm = $pdo->prepare('SELECT * FROM  `user` WHERE `email` = ? OR `phone_number` = ?');
    $stm->execute([$email,$email]);
    $user = $stm->fetch(PDO::FETCH_ASSOC);
    if($user){
        if(password_verify($password, $user['password'])) {
            $pwcheck=true;
            $id = $user['id'];
            $_SESSION['id'] = $id;
            $_SESSION['email'] = $email;
            $_SESSION['isloggedin'] = true;
            header('Location: profile.php?id='.$id);
        } else {
            $pwcheck=false;
        }
    }*/
}
?>
<link href="./assets/css/log_in_register.css" rel="stylesheet">
<div class="container">
    <div class="log_in mt-4 p-4">
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
            <div class="d-flex justify-content-between gap-5 mt-2">
                <input class="form-control" type="text" id="name" name="name" placeholder="First Name" required/>
                <input class="form-control" type="text" id="sname" name="sname" placeholder="Last Name" required/>
            </div>
            <br/>
            <input class="form-control" type="email" id="email" name="email" placeholder="Email" required/>
            <br/>
            <input class="form-control" type="text" id="number" name="number" placeholder="Phone number (optional)"/>
            <br/>
            <input class="form-control" type="password" id="password" name="password" placeholder="Password" required/>
            <br/>
            <input class="form-control" type="password" id="again_password" name="again_password" placeholder="Enter your Password again" required/>
            <br/>
            <?php if(isset($_POST['register_btn'])): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <p class="p-0 m-0 text-dark">
                    <?php if($user && $number_check) echo "This phone number and this email are already on use";
                    else if($user) echo"Email already exists";
                    else if($user == false && $number_check) echo"This phone number is already on use";
                    else if($password_match == false) echo"Passwords doesn't match"
                    ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif ?>
            <div class="d-flex justify-content-between">
                <div>
                    <label for="keep_me_logged"><p>Keep me logged in</p></label>
                    <input class="ms-4 checkbox" type="checkbox" id="keep_me_logged" name="keep_me_logged"/>
                </div>
            <a href='#'>Forgot password?</a>
            </div>
            <div class="d-flex justify-content-between">
                <button class="btn btn-secondary" name='register_btn' type="submit">Register</button>
                <a href="./log_in.php">Log in</a>
            </div>
            
        </form>
    </div>
</div>

<?php 
include 'includes/footer.php';
?>