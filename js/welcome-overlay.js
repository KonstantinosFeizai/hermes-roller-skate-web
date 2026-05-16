document.addEventListener("DOMContentLoaded", function () {
  const overlay = document.getElementById("welcome-overlay");
  const logoWrapper = document.getElementById("logo-appear-wrapper");
  const logoImg = document.getElementById("logo-center");
  const topText = document.getElementById("top-text");
  const bottomText = document.getElementById("bottom-text");

  // ΒΗΜΑ 1: Μόλις τελειώσει το FadeIn (εμφάνιση) του λογοτύπου...
  logoWrapper.addEventListener("animationend", (e) => {
    if (e.animationName === "fadeIn") {
      // ΒΗΜΑ 2: Εμφάνισε τα κείμενα (Arcs)
      topText.style.opacity = "1";
      bottomText.style.opacity = "1";
      topText.classList.add("animate__animated", "animate__backInDown");
      bottomText.classList.add("animate__animated", "animate__backInUp");
    }
  });

  // ΒΗΜΑ 3: Μόλις τελειώσει το animation των κειμένων (ακούμε το bottom-text)...
  bottomText.addEventListener("animationend", (e) => {
    if (e.animationName === "backInUp") {
      // ΒΗΜΑ 4: ΝΕΟ! Κάνε ένα δυνατό Pulse (χτύπημα) στο λογότυπο
      logoImg.classList.add("animate__animated", "animate__pulse");

      // ΒΗΜΑ 5: Μετά από 1 δευτερόλεπτο (αφού τελειώσει το pulse), κλείσε το overlay
      setTimeout(() => {
        overlay.classList.add("fade-out");
      }, 1200); // 1200ms δίνει χρόνο στο pulse (800ms) να ολοκληρωθεί και να "καθίσει"
    }
  });
});
