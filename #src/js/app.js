


@@include("files/regular.js", {});
@@include("files/script.js", {});
@@include("files/dynamic_adapt.js", {});
@@include("files/functions.js", {});
@@include("files/forms.js", {});
@@include("files/scroll.js", {});
@@include("my/clone.js", {});



let burgerClass = document.querySelector(".burger"),
  menuClass = document.querySelector(".menu"),
  menuList = document.querySelector(".menu__list"),
  body = document.querySelector("body"),
  headerMenu = document.querySelector(".header__menu");
// activeClass=document.querySelector(".;
// console.log("burger: ", burgerClass);

burgerClass.addEventListener("click", function (e) {
  burgerClass.classList.toggle("_active");
  menuClass.classList.toggle("_active");
  menuList.classList.toggle("_active");
  headerMenu.classList.toggle("_active");
  body.classList.toggle("_lock");
});



const swiper = new Swiper('.projects-slider__container', {
  // Optional parameters
  // direction: 'vertical',
  loop: true,
  slidesPerView: 3,   //'auto',
  // autoHeight: true,
  //Отключение функционала, если слайдов меньше чем нужно
  watchOverflow: true,
  //Отступ между слайдами
  //spaceBetween: 10,
  // Активный слайд по центру
  // centeredSlides: true,
  //slidesPerGroup: 3,
  // If we need pagination
  // pagination: {
  //   el: '.swiper-pagination',
  // },

  // Navigation arrows
  navigation: {
    nextEl: '.projects-slider-next',
    prevEl: '.projects-slider-prev',
  },

  // And if we need scrollbar
  // scrollbar: {
  //   el: '.swiper-scrollbar',
  // },
});

refreshFsLightbox();


const clientsSwiper = new Swiper('.clients-slider__container', {
  
  // Optional parameters
  // direction: 'vertical',
  loop: true,
  slidesPerView: 5,   //'auto',
  // autoHeight: true,
  //Отключение функционала, если слайдов меньше чем нужно
  watchOverflow: true,
  //Отступ между слайдами
  //spaceBetween: 10,
  // Активный слайд по центру
  // centeredSlides: true,
  //slidesPerGroup: 3,
  // If we need pagination
  // pagination: {
  //   el: '.swiper-pagination',
  // },

  // Navigation arrows
  navigation: {
    nextEl: '.clients-slider-next',
    prevEl: '.clients-slider-prev',
  },

});

console.log('clientsSwiper: ', clientsSwiper);