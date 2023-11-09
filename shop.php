<?php 
include 'includes/header.php';
?>
<link href="./assets/css/shop.css" rel="stylesheet">

<div class="container text-white">
    <?php if(isset($_GET['id'])&&isset($_GET['category'])):?>
    <?php echo"<script>window.location.href='index.php'</script>"?>
    <?php elseif(isset($_GET['category'])):?>
    <?php include 'includes/shop/category.php'?>
    <?php elseif(isset($_GET['id'])):?>
    <?php include 'includes/shop/item.php'?>
    <?php elseif(isset($_GET['search'])):?>
    <?php include 'includes/shop/search.php'?>
    <?php endif?>
</div>
<script>
    document.querySelectorAll('.item').forEach(x=>{
        x.addEventListener('click',()=>{
            window.location.href='./shop.php?id='+x.getAttribute('id').split('-')[1];
        })
    })
</script>
<?php 
include 'includes/footer.php';
?>