// let focusCard = document.querySelectorAll(".focus__card");
// console.log('focusCard: ', focusCard);
// let focusModal = document.querySelector(".focus-modal");
// console.log('focusModal: ', focusModal);
// focusModal.querySelector("div");
// console.log('focusModal.querySelector("div")', focusModal.querySelector("div"));
// let tempFocusItem,
//   cardFocusImg,
//   src = "";
// for (let i = 0; i < focusCard.length; i++) {
//   focusCard[i].addEventListener("click", function () {
//     tempFocusItem = focusCard[i].cloneNode(true);
//     //cardFocusImg = tempFocusItem.querySelector(".card-focus__img");
//     cardFocusImg = tempFocusItem.querySelector("img");
//     console.log("cardFocusImg: ", cardFocusImg);
//     src = cardFocusImg.getAttribute("src");
//     if (src.indexOf("_cr") != -1) {
//       src = src.slice(0, src.length - 6) + ".jpg";
//       cardFocusImg.setAttribute("src", src);

//       if (focusModal.querySelector("div") != null) {
//         focusModal.querySelector("div").remove();
//         focusModal.prepend(tempFocusItem);
//       } else {
//         focusModal.prepend(tempFocusItem);
//       }
//     }
		
//   });
// }
