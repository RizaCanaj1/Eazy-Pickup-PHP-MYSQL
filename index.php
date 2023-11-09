<?php 
include 'includes/header.php';
    $posts = get_posts();
    $on_sale = get_onsale_products();
    $selector=get_posts();
    $get_selector='';
    if(isset($_POST['onsale'])){
        $get_selector='onsale';
        $selector=get_onsale_products();
    }
    else if(isset($_POST['latest'])){
        $get_selector='latest';
        $selector=get_posts();
    }
    else if(isset($_POST['popular'])){
        $get_selector='popular';
        $popular_posts_id=orderby_popular_posts();
        $selector=[];
        foreach($popular_posts_id as $popular_post_id ){
            $selector[]=get_posts_from_id($popular_post_id['post_id']);
        }
    }
    
?>
<link href="./assets/css/index.css" rel="stylesheet">

<div class="container">
    <div class="watched mt-5">
        <?php if($_SESSION['isloggedin']):?>
            <?php $watched = get_watched_posts($_SESSION['id']);?>
            <?php if(count($watched)>0):?>
            <h4>Watched:</h4> 
            <div class="watched-list">
                <?php if(count($watched)>4):?>
                    <?php for($x=0;$x<4;$x++):?>
                        <?php if($x<3):?>
                            <?php 
                                $medias = get_media_from_post_id($watched[$x]['post_id']);
                                $post_category=get_post_category($watched[$x]['post_id']);
                                ?>
                            <div class="w-post d-flex flex-column justify-content-center align-items-center"  ondblclick="window.location.href='shop.php?id=<?=$watched[$x]['post_id']?>'">
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
                                <h6 class="mt-3"><?= $watched[$x]['title']?></h6>
                                <p><?=$watched[$x]['price']-(($watched[$x]['price']*$watched[$x]['onsale'])/100)?>€</p>
                            </div>
                        <?php else :?>
                            <div class="w-post d-flex flex-column justify-content-center align-items-center" onclick="window.location.href='shop.php?shop.php?category=watched'">
                                <i class="fa-solid fa-plus fa-beat fa-xl" style='color: #e3e7ed;'></i>
                                <h4 class="mt-4"><a href='shop.php?category=watched'>View More</a></h4>
                            </div>
                        <?php endif?>
                    <?php endfor?>
                <?php else:?>
                    <?php foreach($watched as $w):?>
                        <?php 
                            $medias = get_media_from_post_id($w['post_id']);
                            $post_category=get_post_category($w['post_id']);
                            ?>
                        <div class="w-post d-flex flex-column justify-content-center align-items-center" ondblclick="window.location.href='shop.php?id=<?=$w['post_id']?>'">
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
                            <h6 class="mt-3"><?= $w['title']?></h6>
                            <p><?=$w['price']-(($w['price']*$w['onsale'])/100)?>€</p>
                        </div>
                    <?php endforeach?>
                <?php endif?>
            </div>
            <?php endif?>
        <?php endif?>
    </div>
</div>

<div class="mt-4 container w-50 selector">
    <form class='d-flex justify-content-between' action="" method='post'>
        <button name='latest'><strong>Latest</strong></button>
        <button name='onsale'><strong>On Sale</strong></button>
        <button name='popular'><strong>Popular</strong></button>
    </form>
</div>
<div class='container selector-wrap d-flex justify-content-betwenn'>
    <button onclick="select_previous()" class="left-btn"><i class="fa-solid fa-chevron-left" style='color: #e3e7ed;'></i></button>
    <div class='selector-posts'>
    </div>
    <button onclick="select_next()" class="right-btn"><i class="fa-solid fa-chevron-right" style='color: #e3e7ed;'></i></button>
</div>

<div class="posts-wall container">
    <?php 
        if(count($posts)>4){
            $posts = array_slice($posts, 0, 4);
        }?>
    <?php foreach($posts as $post):?>
        <?php 
            $id = $post['post_id'];
            $stm = $pdo->prepare('SELECT * FROM `comment` WHERE `comment`.`post_id`=?');
            $stm->execute([$id]);
            $comments = [];
            $explode_time = (explode('-', $post['created_at']));
            $post_time = ['year'=>$explode_time[0],'month'=>$explode_time[1],'day'=>explode(' ', $explode_time[2])[0]];
            while($comment = $stm->fetch(PDO::FETCH_ASSOC)) {
                $comments[] = $comment;
            }
            $nr_of_comments=count($comments);
            $medias = get_media_from_post_id($id);
            $post_category=get_post_category($id);
            $user_from_post = get_user_from_post_id($id);
            ?>
        <div class='post my-3' <?php echo"id='post-$id'"?>>
        <div class='post-from mb-2 d-flex justify-content-between'>
            <i class='fa-solid fa-user' style='color: #e3e7ed;'></i>
                <h6><a href='profile.php?id=<?=$user_from_post['id']?>'><?=$user_from_post['fullname']?></a></h6>
            <div class='d-flex gap-2'>
                <p><?= (str_starts_with($post['created_at'], $current_day))?('Today'):((str_starts_with($post['created_at'], $current_month))?(intval(date('d')-intval($post_time['day'])).' day ago'):((str_starts_with($post['created_at'], $current_year))?(intval(date('m')-intval($post_time['month'])).' month ago'):(intval(date('Y')-intval($post_time['year'])).' year ago')))?></p>
            </div>
        </div>
        <div class='photo-content'>
            <div class='photos'>
                <?php ?>
                <?php if(count($medias)>0):?>
                    <?php foreach($medias as $media):?>
                    <?php if(str_starts_with($media['media_source'],'Post_image')):?>
                    <img src='posts/images/<?=$media['media_source']?>' alt='post-img'><?php if(count($medias)-1>0):?><p><?=count($medias)-1?> other media</p><?php endif?>
                    <?php break;?>
                    <?php elseif(str_starts_with($media['media_source'],'Post_video')):?>
                    <video src='posts/videos/<?=$media['media_source']?>'></video><?php if(count($medias)-1>0):?><p><?=count($medias)-1?> other media</p><?php endif?>
                    <?php break;?>
                    <?php endif?>
                    <?php endforeach?>
                <?php else:?>
                    <img src="default/post-images/<?= strtolower($post_category['name']) ?>-category.png" alt="post-img"><p>This post has 0 photos</p>
                <?php endif?>
            </div>
            <div class='infos'>
                <h4><?= $post['title'] ?></h4>
                <p>Description:</p>
                <p><?= (strlen($post['content'])>120)?(substr($post['content'], 0, 120).'...'):($post['content']) ?></p>
                <div class='d-none'>
                    <?php $post_id=$post['post_id'];?>
                    <div>
                        <p>Categories:</p>
                        <p>Electronic</p>
                        <p><a <?php echo"href='shop.php?id=$post_id'"?> >View More</a></p>
                    </div>
                    <div class='comments d-flex flex-column gap-2'>
                        <?php foreach($comments as $comment):?>
                            <div class="comment">
                                <i class='fa-solid fa-user' style='color: #e3e7ed;'></i>
                                <h6><a href='profile.php?id=1'>Riza Canaj</a></h6>
                                <p class='ms-4 mt-1'><?= $comment['content'] ?></p>
                            </div>
                        <?php endforeach?>
                    </div>
                </div>
                <div class="d-none">Price:<?=$post['price']-(($post['price']*$post['onsale'])/100)?>€</div>
            </div>
        </div>
        <div class='post-icons mt-3 d-flex gap-4'>
            <?php 
                $stm = $pdo->prepare('SELECT * FROM `like_post` WHERE `post_id`=?');
                $stm->execute([$post['post_id']]);
                $likes = [];
                while($like = $stm->fetch(PDO::FETCH_ASSOC)){
                    $likes[]=$like;
                }
            ?>
            <div class='d-flex gap-2'>
                <i class='fa-solid fa-heart mt-1' style='color: #e3e7ed;'></i>
                <p><?= count($likes)?></p>
            </div>
            <div class='d-flex goto-comment gap-2' <?php echo"id='goto-$post_id#comments'"?>>
            <i class='fa-solid fa-comment mt-1' style='color: #e3e7ed;'></i>
                <p><?=$nr_of_comments ?></p>
            </div>
            <i class='fa-solid fa-paper-plane mt-1' style='color: #e3e7ed;'></i>
        </div>
    </div>
    <?php endforeach?>
</div>
<div class="container">
    <div class="categories my-4">
        <div class="bg-danger clothes">
            <h4 class="text-center my-5"><a href="shop.php?category=Clothes">Clothes</a></h4>
        </div>
        <div class="bg-warning electronics">
            <h4 class="text-center my-5"><a href="shop.php?category=Electronics">Electronics</a></h4>
        </div>
        <div class="bg-primary furniture">
            <h4 class="text-center my-5"><a href="shop.php?category=Furniture">Furniture</a></h4>
        </div>
        <div class="bg-success automobiles">
            <h4 class="text-center my-5"><a href="shop.php?category=Automobiles">Automobiles</a></h4>
        </div>
        <div class="bg-dark others">
            <h4 class="text-center my-5"><a href="shop.php?category=Others">Others</a></h4>
        </div>
    </div>
</div>
<div class="container">
    <div class='on-sale mb-5'>
        <?php 
            $o_s_media = get_media_from_post_id($on_sale[0]['post_id']);
            $stm = $pdo->prepare('SELECT `category`.* FROM `category` INNER JOIN `category_post` on `category`.`category_id` = `category_post`.`category_id` where `category_post`.`post_id` = ?');
            $stm->execute([$on_sale[0]['post_id']]);
            $category = $stm->fetch(PDO::FETCH_ASSOC);
            $c_name=$category['name'];
            if(count($on_sale)>7){
                $on_sale = array_slice($on_sale, 0, 4);
                $o_s_media=get_media_from_post_id($on_sale[0]['post_id']);
            }?>
        <div class='main' id='others-id-<?=$on_sale[0]['post_id']?>' ondblclick="window.location.href='shop.php?id=<?=$on_sale[0]['post_id']?>'">
            <?php if(count($o_s_media)>0):?>
                <?php foreach($o_s_media as $media):?>
                <?php if(str_starts_with($media['media_source'],'Post_image')):?>
                <img src='posts/images/<?=$media['media_source']?>' alt='post-img'><?php if(count($medias)-1>0):?><?php endif?>
                <?php break;?>
                <?php elseif(str_starts_with($media['media_source'],'Post_video')):?>
                <video src='posts/videos/<?=$media['media_source']?>'></video><?php if(count($medias)-1>0):?><?php endif?>
                <?php break;?>
                <?php endif?>
                <?php endforeach?>
            <?php else:?>
                <img src="default/post-images/<?= strtolower($c_name) ?>-category.png" alt="">
            <?php endif?>
            <div class="d-flex gap-2 justify-content-center mt-4"><h4><?=$on_sale[0]['price']-(($on_sale[0]['price']*$on_sale[0]['onsale'])/100)?>€</h4><p class="mt-1"><?= $on_sale[0]['price']?>€</p></div>
        </div>
        <div class='others'>
            <?php for($x=0;$x<count($on_sale);$x++):?>
            <?php 
                $stm = $pdo->prepare('SELECT `category`.* FROM `category` INNER JOIN `category_post` on `category`.`category_id` = `category_post`.`category_id` where `category_post`.`post_id` = ?');
                $stm->execute([$on_sale[$x]['post_id']]);
                $category = $stm->fetch(PDO::FETCH_ASSOC);
                $c_name=$category['name'];
            ?>
                <?php $o_s_media=get_media_from_post_id($on_sale[$x]['post_id']);?>
                <?php if($x!==0):?>
                <div class="secondary" id='others-id-<?=$on_sale[$x]['post_id']?>' ondblclick="window.location.href='shop.php?id=<?=$on_sale[$x]['post_id']?>'">
                    <?php if(count($o_s_media)>0):?>
                    <?php foreach($o_s_media as $media):?>
                    <?php if(str_starts_with($media['media_source'],'Post_image')):?>
                    <img src='posts/images/<?=$media['media_source']?>' alt='post-img'><?php if(count($medias)-1>0):?><?php endif?>
                    <?php break;?>
                    <?php elseif(str_starts_with($media['media_source'],'Post_video')):?>
                    <video src='posts/videos/<?=$media['media_source']?>'></video><?php if(count($medias)-1>0):?><?php endif?>
                    <?php break;?>
                    <?php endif?>
                    <?php endforeach?>
                <?php else:?>
                    <img src="default/post-images/<?= strtolower($c_name) ?>-category.png" alt="">
                <?php endif?>
                    <div class="d-flex gap-2 justify-content-center mt-3"><h4><?=$on_sale[$x]['price']-(($on_sale[$x]['price']*$on_sale[$x]['onsale'])/100)?>€</h4><p class="mt-1"><?= $on_sale[$x]['price']?>€</p></div>
                </div>
                <?php endif?>
            <?php endfor?>
        </div>
    </div>
</div>
<script>

</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script>
    let get_data;
    fetch(`includes/CRUD/json.php?selector='<?=$get_selector?>'`)
    .then(response => response.json())
    .then(data => {
        get_data=data;
        for(let x=0;x<3;x++){
            update_selector(x);
        }
    })
    
</script>
<script src="./assets/js/index.js"></script>

<?php 
include 'includes/footer.php';
?>