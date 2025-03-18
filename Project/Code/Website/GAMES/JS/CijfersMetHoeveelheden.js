let numImagesGenerated = 0; // Aantal gegenereerde afbeeldingen
let placedRectangles = []; // Posities van geplaatste afbeeldingen
let score = 0; // Huidige score
let highscore = 0; // Hoogste score
let level = 1; // Huidig level

// Array van lokale afbeeldingen
const localImages = [
  "../AFBEELDINGEN/image1.png",
  "../AFBEELDINGEN/image2.png",
  "../AFBEELDINGEN/image3.png",
  "../AFBEELDINGEN/image4.png",
  "../AFBEELDINGEN/image5.png",
  "../AFBEELDINGEN/image6.png",
];

// Functie om highscore op te slaan in sessionStorage
function saveHighscore() {
  sessionStorage.setItem("highscore", highscore);
}

// Functie om highscore te laden uit sessionStorage
function loadHighscore() {
  const savedHighscore = sessionStorage.getItem("highscore");
  if (savedHighscore !== null) {
    highscore = parseInt(savedHighscore, 10);
  }
}

// Laad highscore bij opstarten
loadHighscore();

function shuffle(array) {
  for (let i = array.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [array[i], array[j]] = [array[j], array[i]];
  }
}

function generateButtons() {
  const container = document.getElementById("buttons-container");
  container.innerHTML = "";

  let numbers = Array.from({ length: 10 }, (_, i) => i + 1);
  shuffle(numbers);

  numbers.forEach((number) => {
    const button = document.createElement("button");
    button.textContent = number;
    button.addEventListener("click", () => handleButtonClick(number));
    container.appendChild(button);
  });
}

// Hulpfunctie: controleren of twee rechthoeken overlappen
function isOverlapping(rectA, rectB) {
  return !(
    rectA.right <= rectB.left ||
    rectA.left >= rectB.right ||
    rectA.bottom <= rectB.top ||
    rectA.top >= rectB.bottom
  );
}

function displayRandomImages() {
  const container = document.getElementById("image-container");
  container.innerHTML = ""; // Oude afbeeldingen wissen
  placedRectangles = []; // Reset posities

  // Hoeveel verschillende soorten afbeeldingen gebruiken? (Max 6)
  let numImageTypes = Math.min(1 + Math.floor(level / 10), localImages.length);

  // Willekeurig gekozen afbeeldingen voor deze ronde
  let selectedImages = [...localImages];
  shuffle(selectedImages);
  selectedImages = selectedImages.slice(0, numImageTypes); // Pak de eerste X willekeurige afbeeldingen

  // Kies een willekeurig aantal afbeeldingen tussen 1 en 10
  numImagesGenerated = Math.floor(Math.random() * 10) + 1;

  const imageWidth = 100;
  const imageHeight = 100;
  const containerWidth = container.clientWidth;
  const containerHeight = container.clientHeight;

  for (let i = 0; i < numImagesGenerated; i++) {
    const img = document.createElement("img");
    img.src = selectedImages[Math.floor(Math.random() * numImageTypes)]; // Elke afbeelding wordt willekeurig gekozen
    img.alt = "Random Image";
    img.style.width = imageWidth + "px";
    img.style.height = imageHeight + "px";
    img.style.position = "absolute";

    let maxAttempts = 50;
    let posX, posY, newRect, overlapping;
    do {
      posX = Math.random() * (containerWidth - imageWidth);
      posY = Math.random() * (containerHeight - imageHeight);
      newRect = {
        left: posX,
        top: posY,
        right: posX + imageWidth,
        bottom: posY + imageHeight,
      };

      overlapping = placedRectangles.some((existingRect) =>
        isOverlapping(newRect, existingRect)
      );
      maxAttempts--;
    } while (overlapping && maxAttempts > 0);

    if (!overlapping) {
      placedRectangles.push(newRect);
      img.style.left = posX + "px";
      img.style.top = posY + "px";
      container.appendChild(img);
    }
  }
}

// Functie om score en highscore bij te werken
function updateScoreDisplay() {
  document.getElementById("score").textContent = "Score: " + score;
  document.getElementById("highscore").textContent =
    "Beste score: " + highscore;
  document.getElementById("level").textContent = "Level: " + level;
}

// Functie om knoppen te verwerken
function handleButtonClick(number) {
  const buttons = document.querySelectorAll("#buttons-container button");

  // Reset knopstijlen en schakel ze in
  buttons.forEach((button) => {
    button.style.backgroundColor = "white";
    button.style.color = "rgb(0, 26, 255)";
    button.disabled = false;
  });

  if (number === numImagesGenerated) {
    // Correct antwoord: verhoog score met aantal soorten afbeeldingen
    let numImageTypes = Math.min(
      1 + Math.floor(level / 10),
      localImages.length
    );
    score += numImageTypes;

    // Update highscore indien nodig
    if (score > highscore) {
      highscore = score;
      saveHighscore();
    }

    level++; // Verhoog level
    updateScoreDisplay();

    buttons.forEach((button) => (button.disabled = true));
    setTimeout(() => {
      document.body.style.backgroundColor = "green";
      setTimeout(() => {
        document.body.style.backgroundColor = "#e6f0ff";
        restartGame(); // Auto-restart na correct antwoord
      }, 500);
    }, 200);
  } else {
    // Verkeerd antwoord: update highscore indien nodig, reset score en level
    if (score > highscore) {
      highscore = score;
      saveHighscore();
    }
    score = 0;
    level = 1; // Reset level bij fout antwoord
    updateScoreDisplay();

    document.body.style.backgroundColor = "red";
    setTimeout(() => {
      document.body.style.backgroundColor = "#e6f0ff";
    }, 500);
  }
}

// Functie om het spel te herstarten
function restartGame() {
  generateButtons();
  displayRandomImages();
}

// Start het spel bij laden van de pagina
window.addEventListener("load", function () {
  generateButtons();
  displayRandomImages();
  updateScoreDisplay();
});
