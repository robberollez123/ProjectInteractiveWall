let port;
const connectButton = document.getElementById("connectButton");
const dataDisplay = document.getElementById("dataDisplay");
const connectionStatus = document.getElementById("connectionStatus");
const dataTable = document.getElementById('data-table-container').getElementsByTagName('tbody')[0];

let currentPage = 1;
const rowsPerPage = 20;
let dataQueue = [];

connectButton.addEventListener("click", async () => {
    try {
        port = await navigator.serial.requestPort();
        await port.open({ baudRate: 9600 });

        connectButton.disabled = true;
        readSerialData();
        updateConnectionStatus(true);
    } catch (err) {
        console.error("Connection failed", err);
        alert("Failed to connect to the serial port.");
    }
});

async function readSerialData() {
    const textDecoder = new TextDecoder();
    const reader = port.readable.getReader();
    let decoderBuffer = "";

    while (true) {
        const { value, done } = await reader.read();
        if (done) break;

        decoderBuffer += textDecoder.decode(value, { stream: true });

        let dataParts = decoderBuffer.split("\n");

        if (dataParts.length > 1) {
            for (let i = 0; i < dataParts.length - 1; i++) {
                if (dataParts[i].trim() !== "") {
                    processReceivedData(dataParts[i].trim());
                }
            }
            decoderBuffer = dataParts[dataParts.length - 1]; // Bewaar incomplete data
        }
    }
}

function processReceivedData(data) {
    if (/^\d+$/.test(data)) { // Check of de data een getal is
        let number = parseInt(data, 10);
        if (number >= 1 && number <= 12) {
            displayData(number);
        }
    }
}

function displayData(number) {
    dataDisplay.textContent = `${number}`; // Update de UI

    // Tijd verkrijgen
    const currentTime = new Date().toLocaleTimeString();

    // Gegevens opslaan en tabel updaten
    dataQueue.push({ time: currentTime, value: number });

    if (dataQueue.length > 1000) {
        dataQueue.shift(); // Verwijder oudste gegevens bij meer dan 1000 entries
    }

    updateTable();
}

function updateTable() {
    dataTable.innerHTML = "";

    // Laatste 20 items weergeven
    const dataToDisplay = dataQueue.slice(-rowsPerPage);

    dataToDisplay.forEach(data => {
        const newRow = dataTable.insertRow();
        const timeCell = newRow.insertCell(0);
        const dataCell = newRow.insertCell(1);

        timeCell.textContent = data.time;
        dataCell.textContent = data.value;
    });

    updatePagination();
}

function updateConnectionStatus(isConnected) {
    connectionStatus.textContent = isConnected ? "Connected" : "Not Connected";
    connectionStatus.style.color = isConnected ? "green" : "red";
}

function updatePagination() {
    const totalPages = Math.ceil(dataQueue.length / rowsPerPage);
    document.getElementById('prevPage').disabled = currentPage === 1;
    document.getElementById('nextPage').disabled = currentPage === totalPages;
    document.getElementById('pageNumber').textContent = `Pagina ${currentPage} van ${totalPages}`;
}
