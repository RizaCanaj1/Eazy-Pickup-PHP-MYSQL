<?php 
include 'includes/header.php';
if($_SESSION['isloggedin'] == false){
    echo "<script>window.location.href='index.php'</script>";
}
else{
    $_SESSION['id'] = null;
    $_SESSION['email'] = null;
    $_SESSION['isloggedin'] = false;
    echo "<script>window.location.href='index.php'</script>";
}

?>