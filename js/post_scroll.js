// post_scroll.js
// Shows a back-to-top button after scrolling a bit and enables smooth scroll to top.
(function () {
  function ready(fn) {
    if (document.readyState !== "loading") {
      fn();
    } else {
      document.addEventListener("DOMContentLoaded", fn);
    }
  }

  ready(function () {
    var btn = document.getElementById("back-to-top");
    if (!btn) return;

    var showAfter = 500; // px scrolled before showing

    function onScroll() {
      if (window.scrollY > showAfter) {
        btn.classList.add("visible");
      } else {
        btn.classList.remove("visible");
      }
    }

    // Throttle scroll handler for perf
    var ticking = false;
    window.addEventListener(
      "scroll",
      function () {
        if (!ticking) {
          window.requestAnimationFrame(function () {
            onScroll();
            ticking = false;
          });
          ticking = true;
        }
      },
      { passive: true },
    );

    // Click: smooth scroll to top
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      try {
        window.scrollTo({ top: 0, behavior: "smooth" });
      } catch (err) {
        // fallback
        window.scrollTo(0, 0);
      }
    });

    // init state
    onScroll();
  });
})();
