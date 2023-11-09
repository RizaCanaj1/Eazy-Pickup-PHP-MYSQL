<?php 
include 'includes/header.php';
if($_SESSION['isloggedin'] == false){
    echo "<script>window.location.href='index.php'</script>";
}
$check_if_empty = (empty($_POST['Title'])&&empty($_POST['Description'])&&empty($_POST['media[]']));
if(isset($_POST['post_btn'])){
    if(isset($_FILES['media']) && !$check_if_empty){
        if($_FILES['media']['size'][0]<1){
            $stm = $pdo->prepare('INSERT INTO `post` (`user_id`,`title`,`content`,`price`) VALUES (?,?,?,?)');
            $stm->execute([$_SESSION['id'],$_POST['Title'],$_POST['Description'],$_POST['price']]);
        }
        else if(empty($_POST['Title'])){
            $stm = $pdo->prepare('INSERT INTO `post` (`user_id`,`content`,`price`) VALUES (?,?)');
            $stm->execute([$_SESSION['id'],$_POST['Description'],$_POST['price']]);
            $last_post_id = get_last_post_id();
            push_media($last_post_id);
        }
        else if(empty($_POST['Description'])){
            $stm = $pdo->prepare('INSERT INTO `post` (`user_id`,`title`,`price`) VALUES (?,?,?)');
            $stm->execute([$_SESSION['id'],$_POST['Title'],$_POST['price']]);
            $last_post_id = get_last_post_id();
            push_media($last_post_id);
        }
        else{
            $stm = $pdo->prepare('INSERT INTO `post` (`user_id`,`title`,`content`,`price`) VALUES (?,?,?,?)');
            $stm->execute([$_SESSION['id'],$_POST['Title'],$_POST['Description'],$_POST['price']]);
            $last_post_id = get_last_post_id();
            push_media($last_post_id);
        }
        $last_post_id = get_last_post_id();
        $category_id = get_category_id($_POST['categories']);
        $stm = $pdo->prepare('INSERT INTO `category_post` (`category_id`,`post_id`) VALUES (?,?)');
        $stm->execute([$category_id,$last_post_id]);
        echo "<script>window.location.href='shop.php?id=$last_post_id'</script>";
    }
    
}
?>
<link href="./assets/css/add_post.css" rel="stylesheet">
<div class="container">
    <div class="add_post mt-5">
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
            <div>
                <div class='form-group'>
                    <label for="Title">Enter product title</label>
                    <input class='form-control' type="text" name='Title'>
                </div>
                <div class='form-group mt-4'>
                    <label for="Description">Description:</label>
                    <textarea class='form-control' name='Description' maxlength="800"></textarea>
                </div>
            </div>
            <div class='form-group ms-5'>
                <div class='media-source my-5'>
                    <a href="#media"><i class="fa-solid fa-download fa-2xl"></i></a>
                </div>
                <div class='get_source'>
                <label for="media[]">Upload pictures of your post:</label>
                <input type="file" name="media[]" class="form-control media" id='media' multiple />
                </div>
            </div>
            <div class='form-group mt-4 mx-5'>
                <select class="form-control text-center" name="categories" id="categories" required>
                    <option value="">----Chose-Categories----</option>
                    <option value="clothes">Clothes</option>
                    <option value="electronics">Electronics</option>
                    <option value="furniture">Furniture</option>
                    <option value="automobiles">Automobiles</option>
                    <option value="others">Others</option>
                </select>
            </div>
            <div class='form-group mt-4 ms-5'>
                <input class='form-control' type="number" name='price' max='100000' placeholder='Enter Price' required>
            </div>
            <div class='form-group mt-4 publish'>
                <?php if(isset($_POST['Title'])&&($check_if_empty)):?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <p class="p-0 m-0 text-dark"> You have to at least fill one of the informations about the product!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></p>
                </div>
                <?php endif?>
                <button class='form-control w-25' name="post_btn">Publish</button>
                
            </div>
        </form>
    </div>
</div>
<script> 
    let media = document.querySelector('.add_post .media');
    update_source(media);
    function update_source(e){
        media = document.querySelector('.add_post .media');
        media.addEventListener('change',e=>{
            update_source(e.target)
        })
        console.log(e);
        if(e.files.length>0){
            if(e.files[0].type.startsWith("image")){
            document.querySelector('.media-source').innerHTML=`<img src="${URL.createObjectURL(e.files[0])}" alt="Test">`
            }
            else if(e.files[0].type.startsWith("video"))(
                document.querySelector('.media-source').innerHTML=`<video src="${URL.createObjectURL(e.files[0])}" width="100%" controls>`
            )
            document.querySelector('.media-source').innerHTML+=`<i class='fa-solid fa-x position-absolute'></i>`
            document.querySelector('.fa-x').addEventListener('click',()=>{
                document.querySelector('.add_post .get_source').innerHTML = `
                <label for="media[]">Upload pictures of your post:</label>
                <input type="file" name="media[]" class="form-control media" id='media' multiple />`;
                media = document.querySelector('.add_post .media');
                update_source(media);
            })
        }
        else{
            document.querySelector('.media-source').innerHTML=`<a href="#media"><i class="fa-solid fa-download fa-2xl"></i></a>`
        }
    }
</script>
<?php 
include 'includes/footer.php';
?>