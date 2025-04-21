<?php
// Database connection
$conn = new mysqli("localhost", "root", "Aanthony912268@", "finase");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve transactions to calculate total amount
$result = $conn->query("SELECT * FROM transactions");
if (!$result) {
    die("Query failed: " . $conn->error);
}

$totalAmount = 0;
while ($row = $result->fetch_assoc()) {
    $totalAmount += $row['amount'];
}

// Fetch investment options based on available balance
$options = [];

// Fetch Recurring Deposits
$sql_rd = "SELECT * FROM RecurringDeposits WHERE min_investment <= ?";
$stmt_rd = $conn->prepare($sql_rd);
$stmt_rd->bind_param("d", $totalAmount);
$stmt_rd->execute();
$result_rd = $stmt_rd->get_result();

while ($row = $result_rd->fetch_assoc()) {
    $row['type'] = 'Recurring Deposit';
    $options[] = $row;
}
$stmt_rd->close();

// Fetch Bonds with Bank Name
$sql_bonds = "SELECT b.*, bank.bank_name FROM Bonds b JOIN Banks bank ON b.bank_id = bank.bank_id WHERE b.minimum_investment <= ?";
$stmt_bonds = $conn->prepare($sql_bonds);
$stmt_bonds->bind_param("d", $totalAmount);
$stmt_bonds->execute();
$result_bonds = $stmt_bonds->get_result();

while ($row = $result_bonds->fetch_assoc()) {
    $row['type'] = 'Bond';
    $options[] = $row;
}
$stmt_bonds->close();

// Fetch Bank Stock Data
$sql_stocks = "SELECT * FROM BankStockData WHERE CurrentPrice <= ?";
$stmt_stocks = $conn->prepare($sql_stocks);
$stmt_stocks->bind_param("d", $totalAmount);
$stmt_stocks->execute();
$result_stocks = $stmt_stocks->get_result();

while ($row = $result_stocks->fetch_assoc()) {
    $row['type'] = 'Bank Stock';
    $options[] = $row;
}
$stmt_stocks->close();

// Fetch Bank Life Insurance Data
$sql_insurance = "SELECT * FROM BankLifeInsurance WHERE premium_range <= ?";
$stmt_insurance = $conn->prepare($sql_insurance); // Prepare the SQL statement

if ($stmt_insurance === false) {
    die("Prepare failed: " . $conn->error); // Handle the error if prepare fails
}

$stmt_insurance->bind_param("d", $totalAmount);
$stmt_insurance->execute();
$result_insurance = $stmt_insurance->get_result();

while ($row = $result_insurance->fetch_assoc()) {
    $row['type'] = 'Life Insurance';
    $options[] = $row;
}
$stmt_insurance->close(); // Close the insurance statement

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investment Options Based on Balance</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* General styles */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #e0f7fa 0%, #e8f5e9 100%);
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h1, h2 {
            text-align: center;
            color: #2e7d32;
            margin-top: 20px;
        }
        h2 {
            color: #388e3c;
        }

        /* Container styles */
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        /* Option card styles */
        .option-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 350px;
            padding: 20px;
            transition: transform 0.3s ease;
        }
        .option-card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .option-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .option-header h3 {
            color: #2e7d32;
            margin: 0;
        }

        /* Option details styles */
        ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        li {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            background: #e8f5e9;
            margin-bottom: 5px;
            border-radius: 5px;
            color: #2c3e50;
        }

        /* Button styles */
        .button {
            display: inline-block;
            margin: 30px 0;
            padding: 15px 30px;
            background: linear-gradient(135deg, #43a047 0%, #66bb6a 100%);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-size: 18px;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: background 0.3s, transform 0.2s;
            text-align: center;
        }
        .button:hover {
            background: linear-gradient(135deg, #388e3c 0%, #43a047 100%);
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <h1>Available Investment Options</h1>
    <h2>Total Balance: ₹<?php echo number_format($totalAmount, 2); ?></h2>

    <div class="container">
    <?php if (!empty($options)): ?>
        <?php foreach ($options as $option): ?>
            <div class="option-card">
                <div class="option-header">
                    <h3><?php echo $option['scheme_name'] ?? $option['bond_type'] ?? $option['bank_name'] ?? $option['plan_name'] ?? $option['BankName']; ?></h3>
                    <span class="type"><?php echo $option['type']; ?></span>
                </div>
                <ul>
                    <li><strong>Bank Name:</strong> <?php echo $option['bank_name'] ?? $option['BankName'] ?? 'N/A'; ?></li>
                    <li><strong>Min Investment:</strong> ₹<?php echo number_format($option['min_investment'] ?? $option['minimum_investment'] ?? $option['premium_range'] ?? $option['CurrentPrice'] ?? '0'); ?></li>
                    <?php if (isset($option['interest_rate'])): ?>
                        <li><strong>Interest Rate:</strong> <?php echo $option['interest_rate']; ?>%</li>
                        <li><strong>Tenure:</strong> <?php echo $option['tenure_range'] ?? ''; ?></li>
                    <?php elseif (isset($option['coupon_rate'])): ?>
                        <li><strong>Coupon Rate:</strong> <?php echo $option['coupon_rate']; ?>%</li>
                        <li><strong>Risk Level:</strong> <?php echo $option['risk_level']; ?></li>
                    <?php elseif (isset($option['CurrentPrice'])): ?>
                        <li><strong>Current Price:</strong> ₹<?php echo number_format($option['CurrentPrice'], 2); ?></li>
                        <li><strong>Market Cap:</strong> ₹<?php echo number_format($option['MarketCap'], 2); ?></li>
                    <?php elseif (isset($option['coverage_options'])): ?>
                        <li><strong>Coverage Options:</strong> <?php echo $option['coverage_options']; ?></li>
                        <li><strong>Policy Tenure:</strong> <?php echo $option['policy_tenure']; ?></li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No suitable investment options found for your balance.</p>
    <?php endif; ?>
    </div>

    <a href="main.php" class="button">Home</a>
</body>
</html>
