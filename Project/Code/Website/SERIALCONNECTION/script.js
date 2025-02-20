let port;
const connectButton = document.getElementById("connectButton");
const dataDisplay = document.getElementById("dataDisplay");
const connectionStatus = document.getElementById("connectionStatus"); // For displaying connection status
const dataTable = document.getElementById('data-table-container').getElementsByTagName('tbody')[0]; // Reference to the table's body

let currentPage = 1; // Current page
const rowsPerPage = 20; // Max rows per page
let dataQueue = []; // All received data stored here

let sortByTimeAscending = true; // Track sort order for time
let sortByDataAscending = true; // Track sort order for received data

// Function to manually connect via button
connectButton.addEventListener("click", async () => {
    try {
        // Manually request the user to select a port
        port = await navigator.serial.requestPort();
        await port.open({ baudRate: 9600 });

        connectButton.disabled = true; // Disable the button when connected
        readSerialData();
        updateConnectionStatus(true); // Update connection status to "connected"
    } catch (err) {
        console.error("Connection failed", err);
        alert("Failed to connect to the serial port.");
    }
});

// Function to start reading serial data
async function readSerialData() {
    const textDecoder = new TextDecoder();
    const readableStream = port.readable;
    const reader = readableStream.getReader();
    let decoderBuffer = "";

    while (true) {
        const { value, done } = await reader.read();
        if (done) break;

        decoderBuffer += textDecoder.decode(value, { stream: true });

        // Split the buffer by newlines to handle multiple data packets
        let dataParts = decoderBuffer.split("\n");

        if (dataParts.length > 1) {
            for (let i = 0; i < dataParts.length - 1; i++) {
                processReceivedData(dataParts[i].trim()); // Process each part
            }
            // Keep any incomplete data to process next time
            decoderBuffer = dataParts[dataParts.length - 1];
        }
    }
}

// Function to process received data and change the color
function processReceivedData(data) {
    if (data) {
        // Display data on screen
        dataDisplay.textContent = data;

        // Add the data to the queue with the timestamp
        const currentTime = new Date().toLocaleTimeString(); // Get the current time
        dataQueue.push({ time: currentTime, value: data });

        // Limit the queue size to 1000 entries
        if (dataQueue.length > 10000) {
            dataQueue.shift(); // Remove the oldest data if we exceed 1000
        }

        // Update the table with the current page data
        updateTable();
    }
}

// Function to update the table with data based on the current page
function updateTable() {
    // Clear the current table content
    dataTable.innerHTML = "";

    // Sort the data queue
    if (sortByTimeAscending) {
        dataQueue.sort((a, b) => {
            const timeA = new Date(`1970-01-01T${a.time}Z`);
            const timeB = new Date(`1970-01-01T${b.time}Z`);
            return timeA - timeB;
        });
    } else {
        dataQueue.sort((a, b) => {
            const timeA = new Date(`1970-01-01T${a.time}Z`);
            const timeB = new Date(`1970-01-01T${b.time}Z`);
            return timeB - timeA;
        });
    }

    if (sortByDataAscending) {
        dataQueue.sort((a, b) => a.value.toLowerCase().localeCompare(b.value.toLowerCase()));
    } else {
        dataQueue.sort((a, b) => b.value.toLowerCase().localeCompare(a.value.toLowerCase()));
    }

    // Calculate start and end index for the current page
    const startIndex = (currentPage - 1) * rowsPerPage;
    const endIndex = startIndex + rowsPerPage;
    const dataToDisplay = dataQueue.slice(startIndex, endIndex);

    // Add the data for the current page to the table
    dataToDisplay.forEach(data => {
        const newRow = dataTable.insertRow(); // Insert a new row at the bottom of the table
        const timeCell = newRow.insertCell(0);
        const dataCell = newRow.insertCell(1);

        timeCell.textContent = data.time;
        dataCell.textContent = data.value;
    });

    // Update pagination controls
    updatePagination();
}

// Function to update the connection status
function updateConnectionStatus(isConnected) {
    if (isConnected) {
        connectionStatus.textContent = "Connected"; // Message
        connectionStatus.style.color = "green"; // Green color for success
        connectionStatus.style.fontWeight = "bold";
    } else {
        connectionStatus.textContent = "Not Connected";
        connectionStatus.style.color = "red"; // Red color for failure
    }
}

// Function to update pagination controls
function updatePagination() {
    const totalPages = Math.ceil(dataQueue.length / rowsPerPage);
    const prevButton = document.getElementById('prevPage');
    const nextButton = document.getElementById('nextPage');

    // Disable/enable pagination buttons based on the current page
    prevButton.disabled = currentPage === 1;
    nextButton.disabled = currentPage === totalPages;

    // Update page number display
    document.getElementById('pageNumber').textContent = `Page ${currentPage} of ${totalPages}`;
}

// Event listener for the "Previous Page" button
document.getElementById('prevPage').addEventListener('click', () => {
    if (currentPage > 1) {
        currentPage--;
        updateTable(); // Update the table with the new page data
    }
});

// Event listener for the "Next Page" button
document.getElementById('nextPage').addEventListener('click', () => {
    const totalPages = Math.ceil(dataQueue.length / rowsPerPage);
    if (currentPage < totalPages) {
        currentPage++;
        updateTable(); // Update the table with the new page data
    }
});

// Event listener for sorting by Time
document.getElementById('sortTime').addEventListener('click', () => {
    sortByTimeAscending = !sortByTimeAscending;
    updateTable();
});

// Event listener for sorting by Received Data
document.getElementById('sortData').addEventListener('click', () => {
    sortByDataAscending = !sortByDataAscending;
    updateTable();
});
