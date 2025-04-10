const navbarMenu = document.querySelector(".navbar .links");
const hamburgerBtn = document.querySelector(".hamburger-btn");
const hideMenuBtn = navbarMenu.querySelector(".close-btn");
const showPopupBtn = document.querySelector(".login-btn");
const formPopup = document.querySelector(".form-popup");
const hidePopupBtn = formPopup.querySelector(".close-btn");
const signupLoginLink = formPopup.querySelectorAll(".bottom-link a");

hamburgerBtn.addEventListener("click", () => {
    navbarMenu.classList.toggle("show-menu");
});

hideMenuBtn.addEventListener("click", () =>  hamburgerBtn.click());

showPopupBtn.addEventListener("click", () => {
    document.body.classList.toggle("show-popup");
});

hidePopupBtn.addEventListener("click", () => showPopupBtn.click());

signupLoginLink.forEach(link => {
    link.addEventListener("click", (e) => {
        e.preventDefault();
        formPopup.classList[link.id === 'signup-link' ? 'add' : 'remove']("show-signup");
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("login-form");
    const signupForm = document.getElementById("signup-form");
    const providerSignupForm = document.getElementById("provider-signup-form");

    const loginBtn = document.getElementById("login-btn");
    const signupLink = document.getElementById("signup-link");
    const providerSignupLink = document.getElementById("provider-signup-link");
    const loginLinks = document.querySelectorAll("#login-link, #login-link-2");

    function showForm(formToShow) {
        [loginForm, signupForm, providerSignupForm].forEach(form => {
            form.style.display = (form === formToShow) ? "flex" : "none";
        });
    }

    signupLink.addEventListener("click", function (e) {
        e.preventDefault();
        showForm(signupForm);
    });

    providerSignupLink.addEventListener("click", function (e) {
        e.preventDefault();
        showForm(providerSignupForm);
    });

    loginBtn.addEventListener("click", function () {
        showForm(loginForm);
    });

    loginLinks.forEach(link => {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            showForm(loginForm);
        });
    });
});