$(window).on("load", function() {
    const vh = Math.max(window.innerHeight || 0);

    if(document.querySelector("body").offsetHeight < vh){
        main = document.querySelector("main");
        header =  document.querySelector("header");

        main.style.height = (vh - header.offsetHeight) + "px";
    }
});