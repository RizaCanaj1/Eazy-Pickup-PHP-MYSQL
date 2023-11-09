<?php 
include 'includes/header.php';
if($_SESSION['isloggedin'] == false){
    echo "<script>window.location.href='index.php'</script>";
}
if(isset($_POST['update_btn'])){
    $fname=$_POST['firstname'];
    $lname=$_POST['lastname'];
    $email=$_POST['email'];
    $pnumber=$_POST['phone_number'];
    $bday=$_POST['bday'];
    $password=$_POST['password'];
    $password_again=$_POST['again_password'];
    $id=$_SESSION['id'];
    if($_POST['again_password']!==$_POST['password']){
        echo"dont";
        $pw_match=false;
    }
    else{
        $pw_match=true;
    }
    $stm = $pdo->prepare('SELECT * FROM  `user` WHERE `email` = ? and `id`!=?');
    $stm->execute([$_POST['email'],$_SESSION['id']]);
    $email_exists = [];
    while($email_exist = $stm->fetch(PDO::FETCH_ASSOC)) {
        $email_exists[] = $email_exist;
    }
    if(count($email_exists)==0){
        if(!empty($password)){
            $stm = $pdo->prepare('UPDATE `user` SET `firstname` = ?, `lastname`= ?, `email`= ?, `password`= ?,`phone_number`= ?,`birthday`=? WHERE `user`.`id` = ?;');
            $stm->execute([$fname,$lname,$email,password_hash($password, PASSWORD_BCRYPT ),$pnumber,$bday,$id]);
            echo "<script>window.location.href='profile.php?id=$id'</script>";
        }
        else{
            $stm = $pdo->prepare('UPDATE `user` SET `firstname` = ?, `lastname`= ?, `email`= ?,`phone_number`= ?,`birthday`=? WHERE `user`.`id` = ?;');
            $stm->execute([$fname,$lname,$email,$pnumber,$bday,$id]);
            echo "<script>window.location.href='profile.php?id=$id'</script>";
        }
    }
}
$user = get_user_from_id($_SESSION['id']);
?>
<link href="./assets/css/edit_profile.css" rel="stylesheet">
<div class='container edit_profile mt-5'>
    <form class='text-white' action="" method="post">
        <div>
            <div class="firstname">
                <label for="firstname">First Name:</label>
                <input class='form-control text-white' type="text" name='firstname' value="<?=$user['firstname']?>" placeholder='<?=$user['firstname']?>' required>
            </div>
            <div class="lastnam">
                <label for="lastname">Last Name:</label>
                <input class='form-control text-white' type="text" name='lastname' value="<?=$user['lastname']?>" placeholder="<?=$user['lastname']?>" required>
            </div>
            
            <div class="bday">
                <label for="bday">Birthday:</label>
                <input class='form-control text-white' type="date" name='bday' value="<?=$user['birthday']?>" placeholder="<?=$user['birthday']?>" required>
            </div>
        </div>
        <div class='me-4'>
            <div class="phone number">
                <label for="phone_number">Phone Number:</label>
                <input class='form-control text-white' type="text" name='phone_number' value="<?=$user['phone_number']?>" placeholder="<?=$user['phone_number']?>">
            </div>
            <div class="email">
                <label for="email">Email:</label>
                <input class='form-control text-white' type="email" name='email' value="<?=$user['email']?>" placeholder="<?=$user['email']?>" required>
            </div>
            <div class="password">
                <label for="password">Password</label>
                <input class='form-control text-white' type="password" name='password' value="" >
                <label for="re-type-password ">Type password again:</label>
                <input class='form-control text-white' type="password" name='again_password' value="" >
            </div>
        </div>
        <div class='update_btn d-flex align-items-center justify-content-center flex-column'>
            <?php if(isset($_POST['update_btn'])): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <p class="p-0 m-0 text-dark">
                    <?php 
                    if($pw_match == false) echo"Passwords doesn't match"
                    ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif ?>
            <button name='update_btn' class='update'>Update</button>
        </div>
    </form>
</div>
<?php 
include 'includes/footer.php';
?>