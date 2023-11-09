<?php 
include 'includes/header.php';
if($_SESSION['isloggedin'] == false){
    echo "<script>window.location.href='index.php'</script>";
}
if(isset($_GET['post_id'])){
    $posts = get_post_from_user_and_post_id($_SESSION['id'],$_GET['post_id']);
    if(is_array($posts)){
        $post = get_posts_from_id($_GET['post_id']);
        if($post['content']=='No Content for this product! We suggest to contact the Owner of this product before purchasing this product.'){
            $post['content']='';
        }
        if($post['title']=='Untitled Product'){
            $post['title']='';
        }
        $post_category=get_post_category($_GET['post_id']);
        $post_category = strtolower($post_category['name']);
        $medias = get_media_from_post_id($_GET['post_id']);
    }
    else{
        echo "<script>window.location.href='index.php'</script>";
    }
}
if(isset($_POST['post_btn'])){
    $check_if_empty = (empty($_POST['Title'])&&empty($_POST['Description'])&&empty($_POST['media[]']));
    if(isset($_FILES['media']) && !$check_if_empty){
        if(empty($_POST['Description'])){
            $stm = $pdo->prepare('UPDATE `post` SET `title`=? , `price`=? WHERE `post`.`post_id` = ?;');
            $stm->execute([$_POST['Title'],$_POST['price'],$_GET['post_id']]);
            push_media($_GET['post_id']);
            echo "1";
        }
        else if(empty($_POST['Title'])){
            $stm = $pdo->prepare('UPDATE `post` SET `content`=?,`price`=? WHERE `post`.`post_id` = ?;');
            $stm->execute([$_POST['Description'],$_POST['price'],$_GET['post_id']]);
            push_media($_GET['post_id']);
            echo "2";
        }
        else{
            $stm = $pdo->prepare('UPDATE `post` SET `title`=? , `content`=? , `price`=? WHERE `post`.`post_id` = ?;');
            $stm->execute([$_POST['Title'],$_POST['Description'],$_POST['price'],$_GET['post_id']]);
            push_media($_GET['post_id']);
            echo "3";
        }
        $category = get_post_category($_GET['post_id'])['name'];
        $category_id = get_category_id($_POST['categories']);
        $stm = $pdo->prepare('UPDATE `category_post` SET `category_id`=? WHERE `post_id` = ?;');
        $stm->execute([$category_id,$_GET['post_id']]);
        echo "<script>window.location.href='index.php'</script>";
    }
}
if(!isset($_POST['post_btn'])&&!isset($_GET['post_id'])){
    echo "<script>window.location.href='index.php'</script>";
}


?>
<link href="./assets/css/add_post.css" rel="stylesheet">
<div class="container">
    <div class="add_post mt-5">
        <form action="" method="post" enctype="multipart/form-data">
            <div>
                <div class='form-group'>
                    <label for="Title">Enter product title</label>
                    <input class='form-control' type="text" name='Title' value='<?= $post['title'] ?>'>
                </div>
                <div class='form-group mt-4'>
                    <label for="Description">Description:</label>
                    <textarea class='form-control' name='Description' maxlength="800"><?= $post['content'] ?></textarea>
                </div>
            </div>
            <div class='form-group ms-5'>
                <div class='media-source my-5'>
                <?php if(count($medias)>0):?>
                    <?php foreach($medias as $media):?>
                        <?php if(str_starts_with($media['media_source'],'Post_image')):?>
                            <img src='posts/images/<?=$media['media_source']?>' alt='post-img'>
                            <?php break;?>
                        <?php elseif(str_starts_with($media['media_source'],'Post_video')):?>
                            <video src='posts/videos/<?=$media['media_source']?>'></video>
                            <?php break;?>
                        <?php endif?>
                    <?php endforeach?>
                    <i class='fa-solid fa-x position-absolute'></i>
                <?php else:?>
                    <a href="#media"><i class="fa-solid fa-download fa-2xl"></i></a>
                <?php endif?>
                    
                </div>
                <div class='get_source'>
                    <?php if(count($medias)==0):?>
                        <label for="media[]">Upload pictures of your post:</label>
                        <input type="file" name="media[]" class="form-control media" id='media' multiple />
                    <?php endif?>
                </div>
            </div>
            <div class='form-group mt-4 mx-5'>
                <select class="form-control text-center" name="categories" id="categories" required value='clothes'>
                    <option value="">----Chose-Categories----</option>
                    <option value="clothes" <?=($post_category=='clothes')?('selected'):('')?>>Clothes</option>
                    <option value="electronics" <?=($post_category=='electronics')?('selected'):('')?>>Electronics</option>
                    <option value="furniture" <?=($post_category=='furniture')?('selected'):('')?>>Furniture</option>
                    <option value="automobiles" <?=($post_category=='automobiles')?('selected'):('')?>>Automobiles</option>
                    <option value="others" <?=($post_category=='others')?('selected'):('')?>>Others</option>
                </select>
            </div>
            <div class='form-group mt-4 ms-5'>
                <input class='form-control' type="number" name='price' max='1000000' placeholder='Enter Price' required value='<?= $post['price'] ?>'>
            </div>
            <div class='form-group mt-4 publish'>
                <?php if(isset($_POST['Title'])&&($check_if_empty)):?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <p class="p-0 m-0 text-dark"> You have to at least fill one of the informations about the product!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></p>
                </div>
                <?php endif?>
                <button class='form-control w-25' name="post_btn">Update</button>
                
            </div>
        </form>
    </div>
</div>
<script> 
    
    let media = document.querySelector('.add_post .media');
    <?php if(count($medias)==0):?>
        update_source(media);
    <?php endif?>
    function update_source(e){
        media = document.querySelector('.add_post .media');
        media.addEventListener('change',e=>{
            update_source(e.target)
        })
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
        <?php if(count($medias)!=0):?> 
            setTimeout(()=>{
                document.querySelector('.fa-x').addEventListener('click',()=>{
                document.querySelector('.add_post .get_source').innerHTML = `
                <label for="media[]">Upload pictures of your post:</label>
                <input type="file" name="media[]" class="form-control media" id='media' multiple />`;
                media = document.querySelector('.add_post .media');
                update_source(media);
            })
            },500)
            
        <?php endif?>
    
</script>
<?php 
include 'includes/footer.php';
?>