let port;
const connectButton = document.getElementById("connectButton");
const resetButton = document.getElementById("resetButton");

const dataDisplay = document.getElementById("dataDisplay");
const connectionStatus = document.getElementById("connectionStatus");
const dataTable = document.getElementById('data-table-container').getElementsByTagName('tbody')[0];

let currentPage = 1;
const rowsPerPage = 20;
let dataQueue = [];
let isConnected = false;
let reader;

// Serial connection handling
connectButton.addEventListener("click", async () => {
    if (isConnected) {
        await disconnectSerial();
    } else {
        try {
            port = await navigator.serial.requestPort();
            await port.open({ baudRate: 9600 });

            connectButton.textContent = "Disconnect";
            isConnected = true;
            updateConnectionStatus(true);
            readSerialData();
        } catch (err) {
            console.error("Connection failed", err);
            alert("Failed to connect to the serial port.");
        }
    }
});

resetButton.addEventListener("click", () => {
    dataQueue = [];
    currentPage = 1;
    updateTable();
    resetButton.disabled = true;
});

async function disconnectSerial() {
    if (reader) {
        await reader.cancel();
        reader.releaseLock();
    }
    if (port) {
        await port.close();
        port = null;
    }
    isConnected = false;
    connectButton.textContent = "Connect";
    updateConnectionStatus(false);
}

async function readSerialData() {
    const textDecoder = new TextDecoder();
    reader = port.readable.getReader();
    let decoderBuffer = "";

    try {
        while (isConnected) {
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
                decoderBuffer = dataParts[dataParts.length - 1]; // Keep incomplete data
            }
        }
    } catch (err) {
        console.error("Error reading serial data", err);
    } finally {
        reader.releaseLock();
    }
}

function processReceivedData(data) {
    if (/^\d+$/.test(data)) { // Validate numeric data
        let number = parseInt(data, 10);
        if (number >= 1 && number <= 12) {
            displayData(number);
        }
    }
}

function displayData(number) {
    dataDisplay.textContent = `${number}`; // Update UI

    // Get current time
    const currentTime = new Date().toLocaleTimeString();

    // Store data and update table
    dataQueue.push({ time: currentTime, value: number });

    if (dataQueue.length > 1000) {
        dataQueue.shift(); // Remove oldest entries beyond 1000
    }

    updateTable();
}

function updateTable() {
    dataTable.innerHTML = "";

    const totalPages = Math.ceil(dataQueue.length / rowsPerPage);
    currentPage = Math.min(currentPage, totalPages) || 1; // Prevent invalid page numbers

    const startIdx = (currentPage - 1) * rowsPerPage;
    const endIdx = startIdx + rowsPerPage;
    const dataToDisplay = dataQueue.slice().reverse().slice(startIdx, endIdx);

    dataToDisplay.forEach(data => {
        const newRow = dataTable.insertRow();
        const timeCell = newRow.insertCell(0);
        const dataCell = newRow.insertCell(1);

        timeCell.textContent = data.time;
        dataCell.textContent = data.value;
    });

    resetButton.disabled = dataQueue.length === 0;
    updatePagination(totalPages);
}

// Pagination controls
document.getElementById('prevPage').addEventListener('click', () => {
    if (currentPage > 1) {
        currentPage--;
        updateTable();
    }
});

document.getElementById('nextPage').addEventListener('click', () => {
    const totalPages = Math.ceil(dataQueue.length / rowsPerPage);
    if (currentPage < totalPages) {
        currentPage++;
        updateTable();
    }
});

// UI updates
function updateConnectionStatus(isConnected) {
    connectionStatus.textContent = isConnected ? "Connected" : "Not Connected";
    connectionStatus.style.color = isConnected ? "green" : "red";
}

function updatePagination(totalPages) {
    document.getElementById('prevPage').disabled = currentPage === 1;
    document.getElementById('nextPage').disabled = currentPage >= totalPages || totalPages === 0;
    document.getElementById('pageNumber').textContent = `Pagina ${currentPage} van ${totalPages || 1}`;
}