<?php 
    $category = $_GET['category'];
    $post_category = $category;
    if($_GET['category']!='watched'){
        $posts = get_posts_from_category($category);
    }
    else if($_SESSION['isloggedin']){
        $posts =  get_watched_posts($_SESSION['id']); 
    }
    else{
        header('Location: index.php');
    }
?>
<div class="mt-4">
    <h2>Category: <?php echo"$category"?></h2>
</div>
<div class="category-items mt-4 mb-4">
    <?php foreach($posts as $post):?>
        <?php 
            $id = $post['post_id'];
            $title = $post['title'];
            $price = $post['price'];
            if($_GET['category']=='watched'){
                $post_category = get_post_category($id);
            }
            $medias = get_media_from_post_id($id);
        ?>
        <div class="item d-flex flex-column align-items-center" <?php echo"id='item-$id'"?>>
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
                <img src="default/post-images/<?= ($_GET['category']=='watched')?(strtolower($post_category['name'])):(strtolower($category)) ?>-category.png" alt="post-img">
            <?php endif?>
            <div class="title-price d-flex">
                <h5><?php echo "$title" ?></h5>
                <p><?php echo "$price"?>€</p>
            </div>
        </div>
    <?php endforeach?>
</div>