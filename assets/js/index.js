var selector =0;
var show_selected =3;

function select_next() {
    if (show_selected < 6) {
        selector += 3;
        show_selected += 3;
    }
    arrows_color_fix();
    document.querySelector('.selector-posts').innerHTML='';
    for(let x= selector; x< show_selected; x++){
        update_selector(x);
    }
    arrows_color_fix();
}

function update_selector(x){
    fetch(`includes/CRUD/json.php?media_from_post_id=${get_data[x].post_id}`)
    .then(response => response.json())
    .then(data => {
        if(data.length>0){
            document.querySelector('.selector-posts').innerHTML+=`<div class='s-post' id="s-post-">
            <div class='ms-3 mt-3 d-flex gap-4'>
                ${(data[0].media_source.startsWith("Post_image"))?(`
                <img src='posts/images/${data[0].media_source}' alt='post-img'> 
                `):(`
                <video src='posts/videos/${data[0].media_source}'></video>
                `)}
                
                <div>
                <h5>${get_data[x].title}</h5>
                    <p>${get_data[x].price-((get_data[x].price*get_data[x].onsale)/100)}€</p>
                    <a href="shop.php?id=${get_data[x].post_id}">View More</a>
                </div>
            </div>
            </div>`;
        }
        else{
            document.querySelector('.selector-posts').innerHTML+=`<div class='s-post' id="s-post-">
            <div class='ms-3 mt-3 d-flex gap-4'>
                <img src='default/post-images/others-category.png' alt='post-img'>
                <div>
                <h5>${get_data[x].title}</h5>
                    <p>${get_data[x].price-((get_data[x].price*get_data[x].onsale)/100)}€</p>
                    <a href="shop.php?id=${get_data[x].post_id}"> View More</a>
                </div>
            </div>
            </div>`;
        }
    })
}
function select_previous(){
    if(selector>0){
        selector-=3;
        show_selected-=3;
    }
    arrows_color_fix();
    document.querySelector('.selector-posts').innerHTML='';
    for(let x= selector; x< show_selected; x++){
        update_selector(x);
    }
}
arrows_color_fix();
function arrows_color_fix(){
    if(selector==0){
        document.querySelector('.left-btn').disabled = true;
        document.querySelector('.left-btn').setAttribute('class','left-btn disabled');
        document.querySelector('.right-btn').disabled = false;
        document.querySelector('.right-btn').setAttribute('class','right-btn');
    }
    else if(show_selected==6){
        document.querySelector('.left-btn').disabled = false;
        document.querySelector('.left-btn').setAttribute('class','left-btn');
        document.querySelector('.right-btn').disabled = true;
        document.querySelector('.right-btn').setAttribute('class','right-btn disabled');
    }
    else{
        document.querySelector('.left-btn').disabled = false;
        document.querySelector('.left-btn').setAttribute('class','left-btn');
        document.querySelector('.right-btn').disabled = false;
        document.querySelector('.right-btn').setAttribute('class','right-btn');
    }
}

const on_sale_others = document.querySelectorAll('.on-sale .others .secondary')
on_sale_others.forEach(other=>{
    other.addEventListener('mouseover',()=>{
        other.setAttribute('class','secondary opened');
    })
    other.addEventListener('mouseout',()=>{
        other.setAttribute('class','secondary');
    })
})
let articles = document.querySelectorAll('.post')
articles.forEach(art=>{
    art.addEventListener('click',()=>{
        art.setAttribute("class", "post my-3 opened-post")
        for(let x of articles){
            if(x.getAttribute('id')!==art.getAttribute('id'))
            x.setAttribute("class", "post my-3")
        }
    })
})
let comments = document.querySelectorAll('.goto-comment')
comments.forEach(comment=>{
    comment.addEventListener('click',()=>{
        window.location.href='shop.php?id='+comment.getAttribute('id').split('-')[1]
    })
})