<?php 
include 'includes/header.php';
if(isset($_GET['id'])){
    $user = get_user_from_id($_GET['id']);
    $posts = get_user_post($_GET['id']);
}
else{
    header('Location: index.php');
}
?>
<link href="./assets/css/profile.css" rel="stylesheet">
<?php if(isset($user)):?>
<div class='container'>
    <div class='user-cover'>
        <img src='https://th.bing.com/th/id/R.c2ee3e23c3c9c8ed0c7797701e6cc574?rik=Pp94vVnCCYRxBQ&pid=ImgRaw&r=0' alt='post-img'>
    </div>
    <div class='user-photo m-auto d-flex justify-content-between align-items-center'>
        <div class="d-flex gap-2">
            <img src="./assets/img/user.png" alt="user">
            <div class='profile-details w-75 m-auto'>
                <h4><?= $user['firstname'].' '.$user['lastname']?></h4>
                <p><?= count($posts)?> <?= (count($posts)>1)?('Posts'):('Post')?></p>
            </div>
        </div>
        <?php if($_SESSION['isloggedin'] && $_GET['id'] == $_SESSION['id']):?>
        <i class="fa-solid fa-pen" onclick='window.location.href="edit_profile.php"'></i>
        <?php endif?>
    </div>
    <?php 
    ?>
    <div class='posts'>
        <?php if(count($posts)>0):?>
        <?php foreach($posts as $post):?>
        <?php $medias = get_media_from_post_id($post['post_id']);$post_category=get_post_category($post['post_id']);?>
        <div class='post d-flex align-items-center justify-content-between' ondblclick='window.location.href="shop.php?id=<?=$post["post_id"]?>"'>
            <div class='content'>
                <h4><?=$post['title'] ?></h4>
                <p><?= (strlen($post['content'])>120)?(substr($post['content'], 0, 120).'...'):($post['content']) ?></p>
                <p><?=$post['created_at'] ?></p>
            </div>
            <div class='photo ms-5'> 
            <?php if(count($medias)>0):?>
                <?php foreach($medias as $media):?>
                    <?php if(str_starts_with($media['media_source'],'Post_image')):?>
                            <img src='posts/images/<?=$media['media_source']?>' alt='post-img'><?php if(count($medias)-1>0):?><?php endif?>
                            <?php break;?>
                        <?php elseif(str_starts_with($media['media_source'],'Post_video')):?>
                            <video src='posts/videos/<?=$media['media_source']?>'></video><?php if(count($medias)-1>0):?><?php endif?>
                            <?php break;?>
                        <?php endif?>
                    <?php endforeach?>
                <?php else:?>
                    <img src="default/post-images/<?= strtolower($post_category['name']) ?>-category.png" alt="post-img">
                <?php endif?>
            </div>
        </div>
        <?php endforeach?>
        <?php else:?>
            <h4>This user doesn't have any post</h4>
        <?php endif?>
        <?php if($_SESSION['isloggedin'] && $_GET['id'] == $_SESSION['id']):?>
        <div class='add-more mb-4 d-flex flex-column align-items-center justify-content-center'>
            <i class="fa-solid fa-plus fa-beat fa-xl" style='color: #e3e7ed;'></i>
            <h4 class="mt-4"><a <?php echo"href='add_post.php'"?>>Add Post</a></h4>
        </div>
        
        <?php endif?>
    </div>
</div>
<?php else:?>
<div class='container user-not-existence'>
    <div class='mt-4'>
    <p class='text-danger'>This User doesn't exist! <a href="index.php">Return to Homepage</a></p>
    </div>
</div>
<?php endif ?>
<?php 
include 'includes/footer.php';
?>