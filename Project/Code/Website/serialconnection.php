<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SerialPort Website Test</title>
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="serialDataTable.css">
    <link rel="stylesheet" href="headerStyle.css">
    <link rel="stylesheet" href="serialConnection.css">
</head>
<body>
    <header>
        <!-- Navbar -->
        <nav>
            <a href="home.php" class="nav-title">Interactive Wall</a>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="games.php">Spellen</a></li>
                <li><a href="#">Seriële connectie</a></li>
                <li><a href="about.php">Over ons</a></li>
            </ul>
        </nav>
    </header>

    <h1>Serial Port Website Test</h1>

    <div class="container">
        <button id="connectButton">Connect</button>
        <div id="connectionStatus">Status: Connected</div>
        <div id="receivedData">Received Data: <span id="dataDisplay">None</span></div>
    </div>
    <!-- Data Table -->
    <!-- Container voor de ingelezen data tabel -->
    <div id="data-table-container">
        <table>
            <thead>
                <tr>
                    <th id="sortTime" style="cursor: pointer;">Tijdstip</th>
                    <th id="sortData" style="cursor: pointer;">Ontvangen Data</th>
                </tr>
            </thead>            
            <tbody>
                <!-- De rijen met data worden hier dynamisch toegevoegd -->
            </tbody>
        </table>
        
        <!-- Paginering -->
        <div id="pagination">
            <button id="prevPage" class="pagination-button" disabled>Vorige</button>
            <span id="pageNumber">Pagina 1 van 1</span>
            <button id="nextPage" class="pagination-button" disabled>Volgende</button>
        </div>
    </div>


    <script src="script.js"></script>
</body>
</html>
