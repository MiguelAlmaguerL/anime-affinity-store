  const btnIrArriba = document.getElementById("btnIrArriba");

  window.addEventListener("scroll", function () {
    if (window.scrollY > 400) {
      btnIrArriba.classList.add("visible");
    } else {
      btnIrArriba.classList.remove("visible");
    }
  });