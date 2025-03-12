document.addEventListener("DOMContentLoaded", function () {
  const menuToggle = document.querySelector(".menu-toggle");
  const navLinks = document.querySelector(".nav-links");

  menuToggle.addEventListener("click", function () {
    navLinks.classList.toggle("active");
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const cards = document.querySelectorAll(".info-card");

  function handleScroll() {
    cards.forEach((card) => {
      const cardPosition = card.getBoundingClientRect().top;
      const screenPosition = window.innerHeight / 1.2;

      if (cardPosition < screenPosition) {
        card.classList.add("visible");
        card.classList.remove("hidden");
      } else {
        card.classList.remove("visible");
        card.classList.add("hidden");
      }
    });
  }

  window.addEventListener("scroll", handleScroll);
  handleScroll();
});

document.addEventListener("DOMContentLoaded", function () {
  const container = document.querySelector(".nav-title"); // Haal de nav-title op
  const text = container.textContent.trim(); // Haal de tekst op uit de HTML

  function animateText() {
    container.innerHTML = ""; // Leegmaken bij elke herstart

    text.split("").forEach((char, index) => {
      let span = document.createElement("span");

      // Controleer of het karakter een spatie is
      if (char === " ") {
        span.classList.add("space"); // Voeg een speciale class toe voor de spatie
      } else {
        span.textContent = char;
        span.classList.add("letter");
      }

      span.style.animationDelay = `${index * 0.1}s`; // 0.1s per letter
      container.appendChild(span);
    });

    // Wacht tot de animatie klaar is, en herstart dan
    //setTimeout(animateText, text.length * 110 + 800); // Wacht tijd = letters + extra pauze
  }

  animateText(); // Start de animatie bij het laden
});
