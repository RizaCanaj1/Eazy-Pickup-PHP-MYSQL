<script>
    let filter_on = false;
    document.querySelector('.searchbar input').addEventListener('focus',()=>{
        document.querySelector('.searchbar i').setAttribute("id", "search-animation")
    });
    document.querySelector('.searchbar input').addEventListener('blur',()=>{
        document.querySelector('.searchbar i').removeAttribute("id")
    });
    document.querySelector('i.fa-filter').addEventListener('click',()=>{
        filter_on=!filter_on;
        check_for_filter();
    });
    function check_for_filter(){
        if(filter_on){
            document.querySelector('.filter').setAttribute("class", "container filter-open")
        }
        else{
            document.querySelector('.filter-open').setAttribute("class", "container filter")
        }
    }
    let start_range=document.querySelector('.price .s-from input')
    let start_price=document.querySelector('.price .s-value')
    start_range.addEventListener('change',e=>{
        start_price.value=e.target.valueAsNumber
        fix_range(e.target.id)
    })
    start_price.addEventListener('change',e=>{
        if(e.target.valueAsNumber>5000){
            e.target.valueAsNumber=5000
        }
        if(e.target.valueAsNumber<0){
            e.target.valueAsNumber=0
        }
        start_range.value=e.target.valueAsNumber
        fix_range(e.target.id)
    })
    let end_range=document.querySelector('.price .e-at input')
    let end_price=document.querySelector('.price .e-value')
    end_range.addEventListener('change',e=>{
        end_price.value=e.target.valueAsNumber
        fix_range(e.target.id)
    })
    end_price.addEventListener('change',e=>{
        if(e.target.valueAsNumber>5000){
            e.target.valueAsNumber=5000
        }
        if(e.target.valueAsNumber<0){
            e.target.valueAsNumber=0
        }
        end_range.value=e.target.valueAsNumber
        fix_range(e.target.id)
    })
    function fix_range(id){
        if(id=='start' && start_price.valueAsNumber>end_price.valueAsNumber) {
            end_price.valueAsNumber=start_price.valueAsNumber
            end_range.valueAsNumber=start_price.valueAsNumber
        }
        if(id=='end' && end_price.valueAsNumber<start_price.valueAsNumber) {
            start_price.valueAsNumber=end_price.valueAsNumber
            start_range.valueAsNumber=end_price.valueAsNumber
        }
    }
    let searchers=document.querySelectorAll('.searcher');
    searchers[0].addEventListener('change',()=>{
        searchers[1].value = searchers[0].value
    })

    
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script src="https://kit.fontawesome.com/51d87a716e.js" crossorigin="anonymous"></script>
</body>
</html>