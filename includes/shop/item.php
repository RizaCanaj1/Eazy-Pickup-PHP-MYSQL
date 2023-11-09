<?php 
    $id = $_GET['id'];
    $post = get_posts_from_id($_GET['id']);
    $likes = get_post_likes($_GET['id']);
    $explode_time = (explode('-', $post['created_at']));
    $post_time = ['year'=>$explode_time[0],'month'=>$explode_time[1],'day'=>explode(' ', $explode_time[2])[0]];
    if(isset($_POST['add_comment'])){
        if(!empty($_POST['comment']))
        add_comment($_SESSION['id'],$_GET['id'],$_POST['comment']);
        $_POST=[];
    }
    $user_from_post =  get_user_from_post_id($id);
    $posts_alike = [];
    if($post){
        $comments = get_comments($_GET['id']);
        $category = get_post_category($id);
        $c_name=$category['name'];
        $stm = $pdo->prepare('SELECT * from `post` INNER JOIN `category_post` on `post`.`post_id`=`category_post`.`post_id` INNER JOIN `category` ON `category`.`category_id`=`category_post`.`category_id` WHERE `category`.`name`=? and `post`.`post_id` !=? ORDER BY rand()');
        $stm->execute([$c_name,$id]);
        while($post_alike  = $stm->fetch(PDO::FETCH_ASSOC)) {
            $posts_alike [] = $post_alike;
        }
    }
    if(count($posts_alike)==0){
        $stm = $pdo->prepare('SELECT * from `post` WHERE `post`.`post_id` !=? ORDER BY rand()');
        $stm->execute([$id]);
        while($post_alike  = $stm->fetch(PDO::FETCH_ASSOC)) {
            $posts_alike [] = $post_alike;
        }
    }
    $medias = get_media_from_post_id($id);
    if($_SESSION['isloggedin']){
        $stm = $pdo->prepare('SELECT count(*) as "views" FROM `views`  WHERE `user_id`=?and `post_id` = ?');
        $stm->execute([$_SESSION['id'],$_GET['id']]);
        $c_v_from_user = $stm->fetch(PDO::FETCH_ASSOC);
        if($c_v_from_user['views'] === 0){
            $stm = $pdo->prepare('INSERT INTO `views` (`user_id`,`post_id`) VALUES (?,?)');
            $stm->execute([$_SESSION['id'],$_GET['id']]);
        }
    }
    $stm = $pdo->prepare('SELECT count(*) as "views" FROM `views`  WHERE `post_id` = ?');
    $stm->execute([$_GET['id']]);
    $views = $stm->fetch(PDO::FETCH_ASSOC);
    $stm = $pdo->prepare('SELECT count(*) as "liked" FROM `like_post`  WHERE `user_id`=? and `post_id` = ?');
    $stm->execute([$_SESSION['id'],$_GET['id']]);
    $liked_from_user = $stm->fetch(PDO::FETCH_ASSOC);
    if(isset($_POST['like'])&&$liked_from_user['liked'] == 0){
        $stm = $pdo->prepare('INSERT INTO `like_post` (`user_id`,`post_id`) VALUES (?,?)');
        $stm->execute([$_SESSION['id'],$_GET['id']]);
    }
    if(isset($_POST['remove_like'])&&$liked_from_user['liked'] != 0){
        $stm = $pdo->prepare('DELETE FROM `like_post` WHERE `user_id`=? and `post_id`=?');
        $stm->execute([$_SESSION['id'],$_GET['id']]);
    }

?>
<?php if($post):?>
<div class="showing-item mt-4 gap-4">
    <?php if(count($medias)>1):?>
        <div id="carouselExampleCaptions" class="carousel slide">
        <div class="carousel-indicators">
            <?php for($x=0;$x<count($medias);$x++):?>
                <?php if($x==0):?>
                    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" <?= ($x==0)?('class="active"'):('')?>  aria-current="true" aria-label="Slide 1"></button>
                <?php else:?>
                    <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="<?=$x?>" aria-label="Slide <?=$x+1?>"></button>
                <?php endif?>
            <?php endfor?>
        </div>
        <div class="carousel-inner d-flex">
            <?php for($x=0;$x<count($medias);$x++):?>
                <div <?= ($x==0)?('class="carousel-item active"'):('class="carousel-item"') ?>>
                <?php if(str_starts_with($medias[$x]['media_source'], 'Post_image')):?>
                    <img src="posts/images/<?= $medias[$x]['media_source'] ?>" alt="<?= $medias[$x]['media_source'] ?>">
                <?php elseif(str_starts_with($medias[$x]['media_source'], 'Post_video')):?>
                    <video src="posts/videos/<?= $medias[$x]['media_source'] ?>" alt="<?= $medias[$x]['media_source']?>" controls></video>
                <?php endif ?>
                </div>
            <?php endfor?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
        </div>
        <?php for($x=0;$x<count($medias);$x++):?>

        <?php endfor?>
    <?php else:?>
       
        <?php if(count($medias)>0):?>
            <?php if(str_starts_with($medias[0]['media_source'], 'Post_image')):?> 
                <div class='single-image'>
                    <img src="posts/images/<?= $medias[0]['media_source'] ?>" alt="<?= $medias[0]['media_source'] ?>">
                </div>
            <?php elseif(str_starts_with($medias[0]['media_source'], 'Post_video')):?>
                <div class="single-video"><video src="posts/videos/<?= $medias[0]['media_source'] ?>" alt="<?= $medias[0]['media_source']?>" controls></video></div>
            <?php endif ?>
        <?php else:?>
            <div class='single-image'>
                <img src="default/post-images/<?= strtolower($c_name) ?>-category.png" alt="">
            </div>
        <?php endif ?>
        
    <?php endif ?>
    <div class="item-details d-flex flex-column justify-content-between">
        <div class="user-details d-flex justify-content-between">
            <p><?= $user_from_post['fullname']?></p>
            <p><?= (str_starts_with($post['created_at'], $current_day))?('Today'):((str_starts_with($post['created_at'], $current_month))?(intval(date('d')-intval($post_time['day'])).' day ago'):((str_starts_with($post['created_at'], $current_year))?(intval(date('m')-intval($post_time['month'])).' month ago'):(intval(date('Y')-intval($post_time['year'])).' year ago')))?></p>
        </div>
        <div class="article-details">
            <h2><?=$post['title'] ?></h2>
            
            <?php ?>
            <div class="category"><h5>Category:</h5><p>
                <a <?php echo"href='shop.php?category=$c_name'"?>><?=$c_name?></a>
                </p></div>
            <div class="price"><h4><?=$post['price']-(($post['price']*$post['onsale'])/100)?>€</h4></div>
            <div class="description"><h5>Description:</h5><p><?=nl2br($post['content'])?></p></div>
        </div>
        <?php if($_SESSION['isloggedin']):?>
        <div class="contact d-flex justify-content-center">
            <?php if($user_from_post['id']!= $_SESSION['id']):?>
                <form action="chat.php?chat_id=<?=$user_from_post['id']?>" method='post'><button class="btn btn-dark">Contact</button></form>
            <?php else:?>
                <form action="edit_post.php?post_id=<?=$id?>" method='post'><button class="btn btn-dark">Edit</button></form>
            <?php endif?>
        </div>
        <div class='post-icons mt-3 d-flex justify-content-around'>
            <?php if($liked_from_user['liked'] == 0):?>
            <form action="" method='post'>
                <button class='like-btn d-flex gap-2' name='like'>
                    <i class='fa-solid fa-heart mt-1' style='color: #e3e7ed;'></i>
                    <p><?= count($likes)?></p>
                </button>
            </form>
            <?php else:?>
            <form action="" method='post'>
                <button class='like-btn d-flex gap-2' name='remove_like'>
                    <i class='fa-solid fa-heart mt-1' style='color: #e3e7ed;'></i>
                    <p><?= count($likes)?></p>
                </button>
            </form>
            <?php endif?>
            <div class='d-flex gap-2'>
                <i class='fa-solid fa-clock mt-1' style='color: #e3e7ed;'></i>
                <p><?= $views['views'] ?></p>
            </div>
        </div>
        <?php else:?>
            <div class='mt-3'></div>
        <?php endif ?>
    </div>
</div>
<?php endif ?>
<div <?= ($post)?('class="comments-view-more my-5 gap-4" id="comments"'):('class="my-5"')?>>
    <?php if($post):?>
    <div class="comments p-4 px-5">
        <div class="d-flex gap-2 justify-content-center">
            <i class='fa-solid fa-comment mt-1' style='color: #e3e7ed;'></i>
            <p><?=count($comments) ?></p>
        </div>
        <div class="send-comment">
            <?php if($_SESSION['isloggedin']):?>
            <form class='d-flex gap-2' action="" method='POST'>
                <input type="text" class='form-control' name='comment'>
                <button class="send-button" name='add_comment'><i class="fa-solid fa-arrow-right"></i></button>
            </form>
            <?php endif?>
        </div>
        <div class="comments-scroll">
            <?php foreach($comments as $comment):?>
            <?php 
                $comment_id=$comment['comment_id'];
                $comment_content=$comment['content'];
                $user_from_comment =  get_user_from_comment_id($comment['comment_id']);
            ?>
            <div class="comment-reply d-flex flex-column align-items-center">
                <div class="comment">
                    <div class="profile-content">
                        <div class='profile-image d-flex justify-content-center align-items-center'>
                            <img src="./assets/img/user.png" alt="">
                        </div>
                        <div class="content">
                            <a href="profile.php?id=<?=$comment['user_id']?>"><?=$user_from_comment['fullname'] ?></a>
                            <p><?php echo "$comment_content"?></p>
                        </div>
                    </div>
                    <?php if($_SESSION['id']!=$comment['user_id']):?>
                    <div class='more d-flex justify-content-center'>
                        <button class='reply-btn' <?php echo "id='replyid-$comment_id'"?>><a>Reply</a></button>
                    </div>
                    <?php endif?>
                </div>
                <div class="reply d-flex justify-content-center" <?php echo "id='replyformid-$comment_id'"?>>
                    <?php if($_SESSION['isloggedin']):?>
                    <form class='reply-form d-flex align-items-center gap-3' action="chat.php?chat_id=<?=$comment['user_id']?>" method='POST'>
                        <input class="form-control reply-input" name='reply' type="text">
                        <input class="d-none" name='postID' type="text" value='<?= $post['post_id']?>'>
                        <input class="d-none" name='content' type="text" value='<?= $comment_content?>'>
                        <button class="btn btn-dark" name='send'>Send</button>
                    </form>
                    <?php else:?>
                        <p class="text-warning">You have to Login first!</p>
                    <?php endif?>
                </div>
            </div>
            <?php endforeach?>
            <?php if(count($comments)==0):?>
                <p class='text-danger'>No comments found for this article, be first to comment</p>
            <?php endif?>
            <?php ?>
            
        </div>
    </div>
    <?php endif ?>
    <div <?= ($post)?('class="view-more"'):('class="view-others"')?>>
        <h3 class="mt-4 ms-4 mb-4"><?= ($post)?('View More:'):('View others:')?></h3>
        <div <?= ($post)?('class="view-more-scroll"'):('class="view-others-scroll"')?>>
            <?php foreach($posts_alike as $post_alike):?>
                <?php  
                    $title=$post_alike['title'];
                    $price=$post_alike['price'];
                    $post_id=$post_alike['post_id'];
                    $p_a_medias=get_media_from_post_id($post_id);
                    $stm = $pdo->prepare('SELECT `category`.* FROM `category` INNER JOIN `category_post` on `category`.`category_id` = `category_post`.`category_id` where `category_post`.`post_id` = ?');
                    $stm->execute([$post_alike['post_id']]);
                    $category = $stm->fetch(PDO::FETCH_ASSOC);
                    $c_name=$category['name'];
                ?>
                <div class="more-item d-flex align-items-center justify-content-center gap-2" <?php echo"ondblclick=\"window.location.href='shop.php?id=$post_id'\"" ?>>
                <?php if(count($p_a_medias)>0):?>
                    <?php foreach($p_a_medias as $media):?>
                    <?php if(str_starts_with($media['media_source'],'Post_image')):?>
                    <img src='posts/images/<?=$media['media_source']?>' alt='post-img'><?php if(count($p_a_medias)-1>0):?><?php endif?>
                    <?php break;?>
                    <?php elseif(str_starts_with($media['media_source'],'Post_video')):?>
                    <video src='posts/videos/<?=$media['media_source']?>'></video><?php if(count($p_a_medias)-1>0):?><?php endif?>
                    <?php break;?>
                    <?php endif?>
                    <?php endforeach?>
                <?php else:?>
                    <img src="default/post-images/<?= strtolower($c_name) ?>-category.png" alt="post-img">
                <?php endif?>
                    <div class="content">
                        <h6 class='title'><?=$post_alike['title']?></h6>
                        <p class='price'><?=$post_alike['price']-(($post_alike['price']*$post_alike['onsale'])/100)?>€</p>
                    </div>
                </div>
            <?php endforeach?>
        </div>
    </div>
</div>
<script>
    let is_replying=[];
    let reply_btn = document.querySelectorAll('.reply-btn');
    reply_btn.forEach(btn=>{
        btn.addEventListener('click',()=>{
            let id=btn.getAttribute('id').split('-')[1]
            is_replying[id]=!is_replying[id];
            for(let x=0;x<is_replying.length;x++){
                if(x!=id && is_replying[x]!=undefined)
                is_replying[x]=false
            }
            check_replying();
        })
    })
    function check_replying(){
        for(let x=0;x<is_replying.length;x++){
            if(is_replying[x]!=undefined){
                if(is_replying[x]){
                    document.querySelector(`#${'replyformid-'+x}`).setAttribute('class','reply-open d-flex justify-content-center')
                    document.querySelector(`#${'replyid-'+x}`).setAttribute('class','reply-btn-clicked')
                }
                else{
                    document.querySelector(`#${'replyformid-'+x}`).setAttribute('class','reply d-flex justify-content-center')
                    document.querySelector(`#${'replyid-'+x}`).setAttribute('class','reply-btn')
                }
            }
            
        }
    }
</script>