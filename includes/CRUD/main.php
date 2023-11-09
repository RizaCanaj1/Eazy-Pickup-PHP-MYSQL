<?php 
include 'database.php';
function get_user_from_id($user_id){
    global $pdo;
    $stm = $pdo->prepare('SELECT * FROM  `user` WHERE `id`=?');
    $stm->execute([$user_id]);
    return $stm->fetch(PDO::FETCH_ASSOC);
}
function get_users($user_id){
    global $pdo;
    $stm = $pdo->prepare('SELECT * FROM  `user`');
    $stm->execute([$user_id]);
    return $stm->fetch(PDO::FETCH_ASSOC);
}
function log_in($login_btn,$email,$password){
    global $pdo;
    $pwcheck=false;
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
    }
    return ['user'=>$user,'password'=>$pwcheck];
}
function get_posts(){
    global $pdo;
    $stm = $pdo->prepare('SELECT * FROM `post` ORDER by `created_at` DESC');
    $stm->execute();
    $posts = [];
    while($post = $stm->fetch(PDO::FETCH_ASSOC)) {
        $posts[] = $post;
    }
    return $posts;
}

function get_posts_from_id($id){
    global $pdo;
    $stm = $pdo->prepare('SELECT * FROM  `post`  WHERE `post`.`post_id`=?');
    $stm->execute([$id]);
    return $stm->fetch(PDO::FETCH_ASSOC);
}
function get_user_post($user_id){
    global $pdo;
    $stm = $pdo->prepare('SELECT `post`.* FROM  `post` INNER JOIN `user` ON `post`.`user_id`=`user`.`id` WHERE `post`.`user_id`=?');
    $stm->execute([$user_id]);
    $posts = [];
    while($post = $stm->fetch(PDO::FETCH_ASSOC)) {
        $posts[] = $post;
    }
    return $posts;
}
function get_post_from_user_and_post_id($user_id,$post_id){
    global $pdo;
    $stm = $pdo->prepare('SELECT `post`.* FROM  `post` INNER JOIN `user` ON `post`.`user_id`=`user`.`id` WHERE `post`.`user_id`=? AND `post`.`post_id`=?');
    $stm->execute([$user_id,$post_id]);
    return $stm->fetch(PDO::FETCH_ASSOC);
}
function get_user_from_post_id($id){
    global $pdo;
    $stm = $pdo->prepare('SELECT `user`.`id`,CONCAT(`user`.`firstname`," ",`user`.`lastname`) as `fullname` FROM `user` INNER JOIN `post` on `user`.`id`=`post`.`user_id` where `post`.`post_id`=?');
    $stm->execute([$id]);
    $user_from_post = $stm->fetch(PDO::FETCH_ASSOC);
    return $user_from_post;
}
function get_user_from_comment_id($id){
    global $pdo;
    $stm = $pdo->prepare('SELECT `user`.`id`,CONCAT(`user`.`firstname`," ",`user`.`lastname`) as `fullname` FROM `user` INNER JOIN `comment` on `user`.`id`=`comment`.`user_id` where `comment`.`comment_id`=?');
    $stm->execute([$id]);
    $user_from_comment = $stm->fetch(PDO::FETCH_ASSOC);
    return $user_from_comment;
}
function get_onsale_products(){
    global $pdo;
    $stm = $pdo->prepare('SELECT * FROM `post` WHERE `onsale`>0 ORDER BY `post`.`onsale` DESC');
    $stm->execute([]);
    $products = [];
    while($product = $stm->fetch(PDO::FETCH_ASSOC)) {
        $products[] = $product;
    }
    return $products;
}
function orderby_popular_posts(){
    global $pdo;
    $stm = $pdo->prepare('SELECT `views`.`post_id`,count(*) as `view_counter` FROM `views` GROUP by `post_id` ORDER by `view_counter` DESC , `post_id` desc');
    $stm->execute();
    $posts = [];
    while($post = $stm->fetch(PDO::FETCH_ASSOC)) {
        $posts[] = $post;
    }
    return $posts;
}
function get_watched_posts($id){
    global $pdo;
    $stm = $pdo->prepare('SELECT `post`.* FROM `post` INNER JOIN `views` on `post`.`post_id`=`views`.`post_id` WHERE `views`.`user_id`=? ORDER by `post`.`post_id` desc');
    $stm->execute([$id]);
    $posts = [];
    while($post = $stm->fetch(PDO::FETCH_ASSOC)) {
        $posts[] = $post;
    }
    return $posts;
}
function get_comments($id){
    global $pdo;
    $stm = $pdo->prepare('SELECT * FROM `comment` WHERE `post_id`=? ORDER BY `created_at` DESC');
    $stm->execute([$id]);
    $comments = [];
    while($comment = $stm->fetch(PDO::FETCH_ASSOC)) {
        $comments[] = $comment;
    }
    return $comments;
}

function get_posts_from_category($category){
    global $pdo;
    $stm = $pdo->prepare('SELECT * FROM `post` INNER JOIN `category_post` on `post`.`post_id`=`category_post`.`post_id` INNER JOIN `category` on `category_post`.`category_id`=`category`.`category_id` where `category`.`name` = ?');
    $stm->execute([$category]);
    $posts = [];
    while($post = $stm->fetch(PDO::FETCH_ASSOC)) {
        $posts[] = $post;
    }
    return $posts;
}
function search($title,$starts,$ends,$category,$order_by){
    global $pdo;
    if($order_by=='cheapest'){
        $order = ' ORDER BY `price`';
    }
    else if($order_by=='most_expensive'){
        $order = ' ORDER BY `price` DESC';
    }
    else if($order_by=='latest'){
        $order = ' ORDER BY `created_at` DESC';
    }
    else if($order_by=='oldest'){
        $order = ' ORDER BY `created_at`';
    }
    else{
        $order = '';
    }
    if(!empty($category)){
        $category=' AND `category_post`.`category_id`='.get_category_id($category);
    }
    else{
        $category='';
    }
    if(!empty($title)){
        $title=' AND `post`.`title` LIKE"%'.$title.'%"';
    }
    else{
        $title='';
    }
    if(!empty($title.$category.$order)||intval($ends)>0){
        $stm = $pdo->prepare('SELECT * FROM `post` INNER JOIN `category_post` on `category_post`.`post_id`=`post`.`post_id` WHERE `post`.`price` BETWEEN ? AND ? '.$title.$category.$order);
        $stm->execute([$starts,$ends]);
        $posts = [];
        while($post = $stm->fetch(PDO::FETCH_ASSOC)) {
            $posts[] = $post;
        }
        return $posts;
    }
    else{
        return get_posts();
    }
        
}
function get_last_post_id(){
    global $pdo;
    $stm = $pdo->prepare('SELECT * FROM `post` order by `post_id` desc limit 1');
    $stm->execute();
    $last_post_id = $stm->fetch(PDO::FETCH_ASSOC);
    return $last_post_id['post_id'];
}
function get_category_id($category){
    global $pdo;
    $stm = $pdo->prepare('SELECT * FROM `category` WHERE `category`.`name` LIKE ?');
    $stm->execute([$category]);
    $category_id = $stm->fetch(PDO::FETCH_ASSOC);
    return  $category_id['category_id'];
}
function get_post_category($id){
    global $pdo;
    $stm = $pdo->prepare('SELECT `category`.* FROM `category` INNER JOIN `category_post` on `category`.`category_id` = `category_post`.`category_id` where `category_post`.`post_id` = ?');
    $stm->execute([$id]);
    return $stm->fetch(PDO::FETCH_ASSOC);
}
function get_last_media_id(){
    global $pdo;
    $stm = $pdo->prepare('SELECT * FROM `media` order by `source_id` desc limit 1');
    $stm->execute();
    $last_media_id = $stm->fetch(PDO::FETCH_ASSOC);
    return $last_media_id['source_id'];
}
function push_media($id){
    global $pdo;
    $stm = $pdo->prepare('DELETE FROM `media_post` where post_id = ?');
    $stm->execute([$id]);
    if($_FILES['media']['size']>1){
        for($i = 0; $i < count($_FILES['media']['name']); $i++) {
            $filename = "Post_".time()."-".$_FILES['media']['name'][$i];
            if(str_starts_with($_FILES['media']['type'][$i], 'image')){
                $filename = "Post_image_".time()."-".$_FILES['media']['name'][$i];
                move_uploaded_file($_FILES['media']['tmp_name'][$i], 'posts/images/'.$filename);
            }
            else if(str_starts_with($_FILES['media']['type'][$i], 'video')){
                $filename = "Post_video_".time()."-".$_FILES['media']['name'][$i];
                move_uploaded_file($_FILES['media']['tmp_name'][$i], 'posts/videos/'.$filename);
            }
            $stm = $pdo->prepare('INSERT INTO `media` (`media_source`) VALUES (?)');
            $stm->execute([$filename]);
            $last_media_id = get_last_media_id();
            $stm = $pdo->prepare('INSERT INTO `media_post` (`source_id`,`post_id`) VALUES (?,?)');
            $stm->execute([$last_media_id,$id]);
        }
    }
}
function get_media_from_post_id($id){
    global $pdo;
    $stm = $pdo->prepare('SELECT `media`.* FROM `media` INNER JOIN `media_post` on `media`.`source_id` = `media_post`.`source_id` where `media_post`.`post_id` = ?');
    $stm->execute([$id]);
    $medias = [];
    while($media = $stm->fetch(PDO::FETCH_ASSOC)) {
        $medias[] = $media;
    }
    return $medias;
}
function get_post_likes($id){
    global $pdo;
    $stm = $pdo->prepare('SELECT * FROM `like_post` where `post_id`=?');
    $stm->execute([$id]);
    $likes = [];
    while($like = $stm->fetch(PDO::FETCH_ASSOC)) {
        $likes[] = $like;
    }
    return $likes;
}
function add_comment($user_id,$post_id,$content){
    global $pdo;
    $stm = $pdo->prepare('INSERT INTO `comment` (`user_id`,`post_id`,`content`) VALUES (?,?,?)');
    $stm->execute([$user_id,$post_id,$content]);
}
?>