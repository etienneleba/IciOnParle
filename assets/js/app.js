/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)

// open and close the menu
window.toggleMenu = function toggleMenu() {
  document.getElementById("navbar").classList.toggle("is-active");
  event.srcElement.classList.toggle("is-active");
};

window.closeNotification = function closeNotification() {
  let notification = event.srcElement.parentNode;
  notification.parentNode.removeChild(notification);
};

window.toggleTarget = function toggleTarget() {
  let target = event.srcElement.dataset.target;
  let targetElement = document.getElementById(target);
  targetElement.classList.toggle("is-active");
};

window.toggleTab = function toggleTab() {
  let target = event.srcElement.dataset.target;
  let targetElement = document.getElementById(target);

  for (var item of document.querySelectorAll(".tabs li.is-active")) {
    item.classList.remove("is-active");
  }

  for (var item of document.querySelectorAll(
    ".tabs-content section.is-active"
  )) {
    item.classList.remove("is-active");
  }

  targetElement.classList.toggle("is-active");
  event.srcElement.parentNode.classList.toggle("is-active");
};
