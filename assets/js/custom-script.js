let toggleOptBtn = document.querySelectorAll('.toggle-options');

for(i = 0; i < toggleOptBtn.length; i++){
    toggleOptBtn[i].addEventListener('click', function(){
        if(!this.parentNode.querySelector('.custom-dd-menu').classList.contains('display-block')){
            this.parentNode.querySelector('.custom-dd-menu').classList.add('display-block');
        } else {
            this.parentNode.querySelector('.custom-dd-menu').classList.remove('display-block');
        }
    });
}
