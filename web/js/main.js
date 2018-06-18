document.addEventListener('DOMContentLoaded', function(event) {
    // Display content
    setTimeout(function() {
        var content = document.getElementById('main-content');
        content.classList.add('main-content--show');
    }, 4000);

    // Navigation
    var navLeft = document.getElementById('navLeft');
    var navRight = document.getElementById('navRight');
    var showData = document.getElementById('showData');
    var episodesData = document.getElementById('episodesData');
    navLeft.addEventListener('click', function() {
        navLeft.classList.add('main-content__nav__link--disabled');
        navRight.classList.remove('main-content__nav__link--disabled');

        episodesData.classList.add('hidden');
        showData.classList.remove('hidden');
    }, false);
    navRight.addEventListener('click', function() {
        navRight.classList.add('main-content__nav__link--disabled');
        navLeft.classList.remove('main-content__nav__link--disabled');

        showData.classList.add('hidden');
        episodesData.classList.remove('hidden');
    }, false);

    // Switch between seasons
    var seasons = document.getElementsByClassName('season-nav__elem__link');
    var current = document.getElementsByClassName('season-nav__elem__link--current');
    current = current[0];
    var currentList = current.getAttribute('id');
    for (cpt = 0;cpt < seasons.length;cpt++) {
        seasons[cpt].addEventListener('click', function() {
            current.classList.remove('season-nav__elem__link--current');
            document.getElementsByClassName(currentList)[0].classList.add('hidden');

            current = this;
            currentList = current.getAttribute('id');
            this.classList.add('season-nav__elem__link--current');
            document.getElementsByClassName(currentList)[0].classList.remove('hidden');
        }.bind(seasons[cpt]));
    }
});