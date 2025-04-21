<?php
// Configuration: Replace with your actual API key or endpoint URL
$nseApiUrl = 'https://api.mock.com/nse/stocks'; // Replace with actual NSE API URL

// Function to make API requests
function fetchFromApi($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Fetch stock data from the mock NSE API
$stocks = [];
$banks = [
    "State Bank of India",
    "ICICI Bank",
    "HDFC Bank",
    "Axis Bank",
    "Kotak Mahindra Bank",
    "Bank of Baroda",
    "Punjab National Bank",
    "Canara Bank",
    "Union Bank of India",
    "IDFC FIRST Bank",
    // Add more banks as needed
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $balance = filter_input(INPUT_POST, 'balance', FILTER_VALIDATE_FLOAT);

    if ($balance !== false && $balance >= 0) {
        // Fetch stock data from the API
        $response = fetchFromApi($nseApiUrl);

        if (isset($response['stocks'])) {
            foreach ($response['stocks'] as $stock) {
                $stocks[] = ['name' => $stock['symbol'], 'price' => $stock['price']];
            }
        } else {
            $error_message = "Failed to fetch stock data.";
        }
    } else {
        $error_message = "Please enter a valid balance.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investment Options Finder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            width: 80%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .error {
            color: red;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Investment Options Finder</h1>
        <form method="POST" action="">
            <label for="balance">Enter your balance:</label>
            <input type="number" id="balance" name="balance" step="0.01" required>
            <button type="submit">Submit</button>
        </form>

        <?php if (isset($error_message)): ?>
            <p class="error"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <?php if (!empty($stocks)): ?>
            <h2>Suitable Stocks:</h2>
            <ul>
                <?php foreach ($stocks as $stock): ?>
                    <?php if ($balance >= $stock['price']): ?>
                        <li><?= htmlspecialchars($stock["name"]) ?>, Price: ₹<?= htmlspecialchars($stock["price"]) ?></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                <p>No suitable stocks found.</p>
            <?php endif; ?>
        <?php endif; ?>

        <h2>List of Banks in India:</h2>
        <ul>
            <?php foreach ($banks as $bank): ?>
                <li><?= htmlspecialchars($bank) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <script>
        // JavaScript code can be added here if needed
    </script>
</body>

</html>