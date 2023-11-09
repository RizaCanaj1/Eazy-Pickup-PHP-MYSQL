<?php 
include 'includes/header.php';
if($_SESSION['isloggedin'] == false){
    echo "<script>window.location.href='index.php'</script>";
}
?>
<?php
if(isset($_POST['reply'])){
    if(!empty($_POST['reply'])){
        $reply = "Reply from: Post_ID: ".$_POST['postID']." - Content: ".$_POST['content']." - - - Replied:".$_POST['reply'];
        $stm = $pdo->prepare('INSERT INTO `chat` (`sender_id`, `reciver_id`, `content`) VALUES (?, ?, ?);');
        $stm->execute([$_SESSION['id'],$_GET['chat_id'],$reply]);
        $stm->fetch(PDO::FETCH_ASSOC);
    }
}
if(isset($_GET['chat_id'])){
    if($_GET['chat_id']==$_SESSION['id']){
        echo "<script>window.location.href='chat.php'</script>";
    }
    else{
        $id = $_GET['chat_id'];
        setcookie('chat_id',$_GET['chat_id']);
    }
}
else{
    if(isset($_COOKIE['chat_id'])){
        setcookie('chat_id', ''); 
    }
}
if(isset($_POST['send_message'])){
    if(!empty($_POST['send_message'])){
    $stm = $pdo->prepare('INSERT INTO `chat` (`sender_id`, `reciver_id`, `content`) VALUES (?, ?, ?);');
    $stm->execute([$_SESSION['id'],$_GET['chat_id'],$_POST['send_message']]);
    $stm->fetch(PDO::FETCH_ASSOC);
    }
}
$stm = $pdo->prepare('SELECT `chat`.*,`user`.`id`,`user`.`firstname`,`user`.`lastname` from `chat` INNER JOIN `user` on `user`.`id` = `chat`.`sender_id` where `chat`.`sender_id`=? or `chat`.`reciver_id`=?');
$stm->execute([$_SESSION['id'],$_SESSION['id']]);
$allchats = [];
while($chat = $stm->fetch(PDO::FETCH_ASSOC)) {
    $allchats[] = $chat;
}
$chat_ids=[];
foreach($allchats as $chat){
    if($chat['id']!=$_SESSION['id']){
        $chat_ids [] = $chat['id'];
    }
}
$chat_ids = array_unique($chat_ids);
$chats = [];
foreach($chat_ids as $chat_id){
    $stm = $pdo->prepare('SELECT `chat`.*,`user`.`id`,`user`.`firstname`,`user`.`lastname` from `chat` INNER JOIN `user` on `user`.`id` = `chat`.`sender_id` where (`chat`.`sender_id`=? and `chat`.`reciver_id`=?) or (`chat`.`sender_id`=? and `chat`.`reciver_id`=?)');
    $stm->execute([$chat_id,$_SESSION['id'],$_SESSION['id'],$chat_id]);
    $chats = [];
    while($chat = $stm->fetch(PDO::FETCH_ASSOC)) {
        $chats[] = $chat;
    }
}
$stm = $pdo->prepare('SELECT `chat`.*,`user`.`id`,`user`.`firstname`,`user`.`lastname` from `chat` INNER JOIN `user` on `user`.`id` = `chat`.`sender_id` where (`chat`.`sender_id`=? and `chat`.`reciver_id`=?) or (`chat`.`sender_id`=? and `chat`.`reciver_id`=?)  ORDER BY `chat`.`created_at`');
$stm->execute([$id,$_SESSION['id'],$_SESSION['id'],$id]);
$opened_chat = [];
while($chat=$stm->fetch(PDO::FETCH_ASSOC)){
    $opened_chat[]=$chat;
}
?>
<link href="./assets/css/chat.css" rel="stylesheet">
<div class='container'>
    <div class="friends-chat">
        <div class="friends-open">
            <?php foreach($chat_ids as $chat_id):?>
                <?php 
                    $stm = $pdo->prepare('SELECT `chat`.*,`user`.`id`,`user`.`firstname`,`user`.`lastname` from `chat` INNER JOIN `user` on `user`.`id` = `chat`.`sender_id` where (`chat`.`sender_id`=? and `chat`.`reciver_id`=?) or (`chat`.`sender_id`=? and `chat`.`reciver_id`=?) ORDER BY `chat`.`created_at`');
                    $stm->execute([$chat_id,$_SESSION['id'],$_SESSION['id'],$chat_id]);
                    $conversations=[];
                    while($conversation = $stm->fetch(PDO::FETCH_ASSOC)) {
                        $conversations[] = $conversation;
                    }
                    $last_chat = end($conversations);
                    $content = $last_chat['content'];
                    $stm = $pdo->prepare('SELECT `chat`.*,`user`.`id`,`user`.`firstname`,`user`.`lastname` from `chat` INNER JOIN `user` on `user`.`id` = `chat`.`sender_id` where (`chat`.`sender_id`=? and `chat`.`reciver_id`=?) or (`chat`.`sender_id`=? and `chat`.`reciver_id`=?) and `user`.`id`!=?');
                    $stm->execute([$chat_id,$_SESSION['id'],$_SESSION['id'],$chat_id,$_SESSION['id']]);
                    $id_name_fixer=[];
                    while($fixer = $stm->fetch(PDO::FETCH_ASSOC)) {
                        $id_name_fixer[] = $fixer;
                    }
                    $fix = end($id_name_fixer);
                    $last_chat_id=$fix['id'];
                    $fullname = $fix['firstname'].' '.$fix['lastname'];
                ?>
            <div class="friend m-4 d-flex gap-3 justify-content-between" <?php echo"id='chatid-$last_chat_id'"?>>
                <img src="./assets/img/user.png" alt="user" <?php echo"id='chatid-$last_chat_id'"?>>
                <div>
                    <p <?php echo"id='chatid-$last_chat_id'"?>><?=$fullname?></p>
                    <p <?php echo"id='chatid-$last_chat_id'"?>><?= $content?></p>
                </div>
                <div <?php echo"id='chatid-$last_chat_id'"?>>
                    <p <?php echo"id='chatid-$last_chat_id'"?>>Today</p>
                </div>
            </div>
            <?php endforeach?>
        </div>
        <div class="chat">
            <?php if(isset($_GET['chat_id'])):?>
                <div class="m-auto my-4 in-chat">
                    <?php foreach($opened_chat as $chat):?>
                    <?php if ($chat['sender_id']==$_SESSION['id']):?>
                        <div class='you d-flex gap-2 p-4 pb-0 flex-row-reverse align-items-center'>
                            <img src="./assets/img/user.png" alt="user">
                            <p><?= $chat['content']?></p>
                        </div>
                    <?php else:?>
                        <div class='others d-flex gap-2 p-4 pb-0 align-items-center'>
                            <img src="./assets/img/user.png" alt="user">
                            <p><?= $chat['content']?></p>
                        </div>
                    <?php endif ?>
                    <?php endforeach ?>
                </div>
                <form class="send-message d-flex align-items-center justify-content-center gap-3" method="post">
                    <input class="form-control" name='send_message' type="text">
                    <button class="send-button"><i class="fa-solid fa-arrow-right"></i></button>
                </form>
            <?php endif?>
        </div>
    </div>
</div>
<script>
    let chat_show=false
    let cookies=document.cookie.split(';');
    <?php if(isset($_GET['chat_id'])):?>
    document.querySelector('.in-chat').scrollTo(0,document.querySelector('.in-chat').scrollHeight);
    <?php endif?>
    function getCookie(key){
    for (let cookie of cookies) {
        if (cookie.split('=')[0] == key) {
            return `${cookie.split('=')[1]}`;
        }
    }
    return undefined;
    }
    
    if(getCookie('chat_id')){
        chat_show=true;
        open_chat(getCookie('chat_id'))
    }
    else{
        chat_show=false;
    }
    let friends = document.querySelectorAll('.friend');
    friends.forEach(friend=>{
        friend.addEventListener('click',e=>{
            new_chat(e.target.id.split('-')[1])
        })
    })
    function open_chat(id){
        if(!chat_show){
            if(id!==undefined){
                chat_show=true;
                window.location.href=`chat.php?chat_id=${id}`
            }
        }
        if(chat_show){
            console.log(id)
            document.querySelector('.friends-open').setAttribute('class','friends')
            document.querySelector('.chat').setAttribute('class','chat')
        }
    }
    function new_chat(id){
        if(id!==undefined)
        window.location.href=`chat.php?chat_id=${id}`
    }
</script>
<?php 
include 'includes/footer.php';
?>