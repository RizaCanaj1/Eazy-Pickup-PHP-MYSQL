<?php

header('Content-Type: application/json');
include 'main.php';
    $posts = get_posts();
    function selector_order($post){
        global $posts;
        if(str_contains($post,'onsale')){
            $posts=get_onsale_products();
        }
        if(str_contains($post,'latest')){
            $posts=get_posts();
        }
        if(str_contains($post,'popular')){
            $popular_posts_id=orderby_popular_posts();
            $posts=[];
            foreach($popular_posts_id as $popular_post_id ){
                $posts[]=get_posts_from_id($popular_post_id['post_id']);
            }
        }
        echo json_encode($posts);
    }
    if(isset($_GET['selector'])){
        selector_order($_GET['selector']);
    }
    function get_media($id){
        $media = get_media_from_post_id($id);
        echo json_encode($media);
    }
    if(isset($_GET['media_from_post_id'])){
        get_media($_GET['media_from_post_id']);
    }
?>