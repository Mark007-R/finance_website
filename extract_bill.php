<?php
// Database configuration
$host = 'localhost'; // Your database host
$db = 'finase'; // Your database name
$user = 'root'; // Update with your DB username
$pass = 'Aanthony912268@'; // Update with your DB password

// Function to connect to the database
function getDatabaseConnection()
{
    global $host, $db, $user, $pass;
    $mysqli = new mysqli($host, $user, $pass, $db);

    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    return $mysqli;
}

function extractTextFromPDF($pdfFilePath)
{
    $desktopPath = 'Desktop'; // Update this to your actual Desktop path
    $outputFilePath = $desktopPath . 'extracted_text.txt';
    $pdftotextPath = 'C:\\xampp\\htdocs\\website\\poppler-24.07.0\\Library\\bin\\pdftotext.exe'; // Absolute path to pdftotext

    // Escape file paths to handle spaces and special characters
    $escapedPdfFilePath = escapeshellarg($pdfFilePath);
    $escapedOutputFilePath = escapeshellarg($outputFilePath);

    // Command to extract text from PDF using pdftotext
    $command = "\"$pdftotextPath\" \"$escapedPdfFilePath\" \"$escapedOutputFilePath\" 2>&1";
    exec($command, $output, $returnVar);

    // Output for debugging
    echo '<pre>' . htmlspecialchars(implode("\n", $output)) . '</pre>';

    if ($returnVar === 0) {
        if (file_exists($outputFilePath)) {
            $text = file_get_contents($outputFilePath);
            return $text;
        } else {
            throw new Exception('Output file not found.');
        }
    } else {
        throw new Exception('Error extracting text from PDF. Command output: ' . implode("\n", $output));
    }
}

function findBillDetails($text)
{
    $billAmount = 'Not found';
    $billDate = 'Not found';

    // Regex to find amounts in the text
    preg_match_all('/\b\d+\.\d{2}\b/', $text, $amountMatches);
    if ($amountMatches[0]) {
        $billAmount = $amountMatches[0][0]; // Assuming the first match is the bill amount
    }

    // Regex to find dates in the text (format: MM/DD/YYYY or DD/MM/YYYY)
    preg_match_all('/\b(\d{1,2}[\/.-]\d{1,2}[\/.-]\d{2,4})\b/', $text, $dateMatches);
    if ($dateMatches[0]) {
        $billDate = $dateMatches[0][0]; // Assuming the first match is the bill date
    }

    // Convert date format to YYYY-MM-DD
    if ($billDate !== 'Not found') {
        $dateTime = DateTime::createFromFormat('m/d/Y', $billDate);
        if (!$dateTime) {
            // Try different format if necessary
            $dateTime = DateTime::createFromFormat('d/m/Y', $billDate);
        }
        if ($dateTime) {
            $billDate = $dateTime->format('Y-m-d');
        } else {
            $billDate = 'Invalid date';
        }
    }

    return [$billAmount, $billDate];
}

$billAmount = '';
$billDate = '';
$errorMessage = '';
$debugText = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['pdf']['tmp_name'];

        try {
            $text = extractTextFromPDF($fileTmpPath);
            $debugText = htmlspecialchars($text); // Add debug text
            list($billAmount, $billDate) = findBillDetails($text);

            // Convert amount to negative for database insertion
            // Convert amount to negative for database insertion
            if ($billAmount !== 'Not found') {
                $billAmount = -abs(floatval($billAmount)); // Ensure it's negative
            } else {
                $billAmount = 0.0; // Default to 0.0 if not found
            }


            // Insert into the database
            $mysqli = getDatabaseConnection();
            $description = 'Bill'; // Set the description
            $stmt = $mysqli->prepare("INSERT INTO transactions (description, amount, date) VALUES (?, ?, ?)");
            $stmt->bind_param("sds", $description, $billAmount, $billDate);

            if ($stmt->execute()) {
                // Data inserted successfully
            } else {
                throw new Exception('Error inserting data: ' . $stmt->error);
            }
            $stmt->close();
            $mysqli->close();
        } catch (Exception $e) {
            $errorMessage = 'Error processing PDF: ' . $e->getMessage();
        }
    } else {
        $errorMessage = 'File upload error.';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extract Bill Amount from PDF</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* General Reset */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* Body Styles */
        body {
            font-family: 'Roboto', sans-serif;
            padding: 40px;
            text-align: center;
            background-color: #e9f7ef;
            /* Light green background */
            color: #2e7d32;
            /* Dark green for text */
        }

        /* Headings */
        h1 {
            margin-bottom: 30px;
            color: #388e3c;
            /* Medium green for headings */
            font-size: 2.5em;
        }

        /* Form Styles */
        form {
            margin-bottom: 30px;
            background: #ffffff;
            /* White background for form area */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            display: inline-block;
            width: 100%;
            max-width: 500px;
            /* Responsive max width */
        }

        /* File Input Styles */
        input[type="file"] {
            margin-bottom: 15px;
            padding: 12px;
            border: 2px solid #4caf50;
            /* Green border */
            border-radius: 5px;
            font-size: 1em;
            width: 100%;
        }

        /* Button Styles */
        button {
            padding: 12px 25px;
            background-color: #4caf50;
            /* Bright green for the button */
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            transition: background-color 0.3s, transform 0.2s;
            /* Smooth transition */
        }

        button:hover {
            background-color: #388e3c;
            /* Darker green on hover */
            transform: translateY(-2px);
            /* Lift effect on hover */
        }

        /* Result Styles */
        #result {
            margin-top: 30px;
            font-size: 1.5em;
            background: #ffffff;
            /* White background for result area */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            display: inline-block;
            max-width: 500px;
            /* Responsive max width */
        }

        /* Error Message Styles */
        .error {
            color: #d32f2f;
            /* Red for errors */
            font-size: 1.2em;
        }

        /* Debug Information Styles */
        .debug {
            margin-top: 30px;
            padding: 20px;
            background: #ffffff;
            /* White background for debug area */
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: left;
            white-space: pre-wrap;
            max-width: 800px;
            /* Limit width of debug area */
            margin-left: auto;
            margin-right: auto;
        }

        /* Responsive Styles */
        @media (max-width: 600px) {
            body {
                padding: 20px;
                /* Reduce padding on smaller screens */
            }

            h1 {
                font-size: 2em;
                /* Smaller heading on mobile */
            }

            button {
                font-size: 1em;
                /* Smaller button text */
            }
        }
    </style>
</head>

<body>
    <h1>Extract Bill Amount from PDF</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="pdf" accept=".pdf" required>
        <button type="submit">Upload PDF</button>
        <button><a href="main.php" style="text-decoration: none; color:white; font-family:Verdana, Geneva, Tahoma, sans-serif">Home</a></button>
    </form>
    <div id="result">
        <?php if ($errorMessage): ?>
            <p class="error"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php else: ?>
            <p>Bill Amount: Rs<?php echo htmlspecialchars($billAmount); ?></p> <!-- Show positive amount -->
            <p>Bill Date: <?php echo htmlspecialchars($billDate); ?></p>
        <?php endif; ?>
    </div>
    <?php if ($debugText): ?>
        <div class="debug">
            <h2>Extracted Text</h2>
            <pre><?php echo $debugText; ?></pre>
        </div>
    <?php endif; ?>
</body>

</html>