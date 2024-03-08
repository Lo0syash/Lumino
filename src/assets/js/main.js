document.querySelector('.open-burger').addEventListener('click', burgerMenu);
document.querySelector('.outline').addEventListener('click', burgerMenu);
document.querySelector('.close-btn').addEventListener('click', burgerMenu);
function burgerMenu(){
    if (window.innerWidth <= 1350) {
        document.querySelector('.aside').classList.toggle('active');
        document.querySelector('.outline').classList.toggle('active');
    }
}

document.querySelector('.music-more--btn').addEventListener('click', () => {
    document.querySelector('.music-list').classList.add('active');
    document.querySelector('.music-more--btn').classList.add('active');
})

