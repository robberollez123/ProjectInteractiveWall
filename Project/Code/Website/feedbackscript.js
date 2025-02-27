document.addEventListener("DOMContentLoaded", function () {
    const menuToggle = document.querySelector(".menu-toggle");
    const navLinks = document.querySelector(".nav-links");

    menuToggle.addEventListener("click", function () {
        navLinks.classList.toggle("active");
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("myModal");
    const modalMessage = document.getElementById("modalMessage");
    const closeModal = document.querySelector(".close");

    // Verberg de modal standaard
    modal.style.display = "none";

    // Simuleer een succesvol verzonden bericht (wanneer "send_feedback.php" feedback verwerkt)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has("success")) {
        modalMessage.textContent = "Bedankt voor je feedback! 😊";
        modal.style.display = "flex";
    }

    // Sluitknop functionaliteit
    closeModal.addEventListener("click", function () {
        modal.style.display = "none";
    });

    // Sluiten bij klikken buiten modal
    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
});
