<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "Aanthony912268@"; // Use your database password
$dbname = "finase";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];

    if (!empty($description) && !empty($amount) && !empty($date)) {
        $stmt = $conn->prepare("INSERT INTO transactions (description, amount, date) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $description, $amount, $date);

        if ($stmt->execute()) {
            $message = "Transaction added successfully!";
        } else {
            $message = "Error adding transaction.";
        }
        $stmt->close();
    }
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM transactions WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $message = "Transaction deleted successfully!";
        } else {
            $message = "Error deleting transaction.";
        }
        $stmt->close();
    }
}

// Retrieve transactions
$result = $conn->query("SELECT * FROM transactions");

$transactions = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finase</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;400;700&display=swap" rel="stylesheet">
    <style>
        /* Global Variables */
        :root {
            --dark-blue: #2D4A22;
            --green: #49aa26;
            --light-green: #3dd705;
            --red: #e92929;
            --white: #ffffff;
            --background: #f5f7f9;
            --text-color: #606c76;
            --card-bg: #ffffff;
            --card-border-radius: 0.5rem;
            --font-family: 'Poppins', sans-serif;
            --font-size-base: 15px;
            --transition: 0.3s ease-in-out;
        }

        /* Global Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            font-size: 93.75%;
            /* 15px */
        }

        body {
            background: var(--background);
            font-family: var(--font-family);
            color: var(--dark-blue);
            line-height: 1.6;
        }

        /* Prevent scrolling */
        .no-scroll {
            overflow: hidden;
        }

        /* Visually hidden */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }

        /* Balance Container */
        .balance-container {
            margin-bottom: 2rem;
        }

        #balance {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem auto;
        }

        /* Cards */
        .card {
            margin: 0px 10px 0px 20px;
            background: var(--card-bg);
            padding: 2rem;
            border-radius: var(--card-border-radius);
            color: var(--dark-blue);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: box-shadow var(--transition), transform var(--transition);
            width: 100%;
            max-width: 350px;
            /* Set max-width to control the width of the boxes */
        }

        .card:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .card.total {
            background: var(--green);
            color: var(--white);
        }

        .card h3 {
            font-weight: 400;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card p {
            font-weight: 700;
            font-size: 1.8rem;
            line-height: 2.4rem;
            margin-top: 0.5rem;
        }

        /* Header */
        header {
            background: var(--dark-blue);
            padding: 1rem 2rem;
            text-align: center;
            color: var(--white);
            position: relative;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #logo {
            font-size: 1.8rem;
            font-weight: 300;
        }

        #logo img {
            vertical-align: middle;
            margin-left: 10px;
        }

        .logout-button {
            text-decoration: none;
            background-color: var(--red);
            color: var(--white);
            border: 2px solid black;
            padding: 0.6rem 1.2rem;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            cursor: pointer;
            transition: background-color var(--transition), color var(--transition), box-shadow var(--transition);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 0.3rem;
            /* Rounded edges */
        }

        .logout-button:hover {
            background-color: darkred;
        }

        .logout-button:active {
            background-color: #b30000;
        }

        .logout-button:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.3);
        }

        /* New Transaction Button */
        /* Button Styles */
        .button {
            text-decoration:none;
            background-color: var(--green);
            color: var(--white);
            border: none;
            border-radius: 0.3rem;
            padding: 0.8rem 1.6rem;
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            cursor: pointer;
            transition: background-color var(--transition), box-shadow var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .button:hover {
            background-color: var(--light-green);
        }

        .button:active {
            background-color: #3d9a20;
        }

        .button:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.3);
        }

        header>div {
            display: flex;
            align-items: center;
            /* Aligns buttons vertically */
        }

        .header-button {
            margin-right: 1rem;
            /* Space between the buttons */
        }

        /* Table */
        #transaction {
            margin-top: 1rem;
            margin-left: 2rem;
            margin-right: 2rem;
        }

        #data-table {
            width: 100%;
            border-spacing: 0 0.5rem;
            border-collapse: separate;
        }

        table thead tr th {
            background: #f9f9f9;
            font-weight: 600;
            padding: 1rem;
            color: var(--text-color);
            text-align: left;
            border-bottom: 2px solid #ddd;
        }

        table tbody tr {
            background: var(--card-bg);
            transition: background-color var(--transition);
        }

        table tbody tr:hover {
            background-color: #f1f1f1;
        }

        table tbody td {
            padding: 1rem;
        }

        /* Modal */
        .modal-overlay {
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            position: fixed;
            top: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity var(--transition), visibility var(--transition);
            z-index: 999;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal {
            background: var(--white);
            padding: 1.5rem;
            position: relative;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Form */
        #form {
            max-width: 400px;
        }

        #form h2 {
            margin-top: 0;
            margin-bottom: 1rem;
            font-size: 1.4rem;
            font-weight: 600;
        }

        #form form input {
            border: 1px solid #ddd;
            border-radius: var(--card-border-radius);
            padding: 0.8rem;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 1rem;
            transition: border-color var(--transition);
        }

        #form form input:focus {
            border-color: var(--green);
            outline: none;
        }

        .input-group {
            margin-bottom: 1rem;
        }

        .input-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .input-group .help {
            opacity: 0.6;
            font-size: 0.75rem;
        }

        .input-group.actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .input-group.actions .button,
        .input-group.actions button {
            width: 48%;
            padding: 0.6rem 1rem;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 3rem 0 2rem;
            color: var(--text-color);
            font-size: 0.875rem;
        }

        /* Form Container */
        #form {
            background: var(--white);
            border-radius: var(--card-border-radius);
            padding: 2rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            margin: auto;
        }

        /* Form Heading */
        #form h2 {
            margin-top: 0;
            margin-bottom: 1.5rem;
            font-size: 1.6rem;
            font-weight: 600;
            color: var(--dark-blue);
        }

        /* Form Input Styles */
        #form form input {
            border: 1px solid #ddd;
            border-radius: var(--card-border-radius);
            padding: 0.8rem 1rem;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 1rem;
            font-size: 1rem;
            color: var(--dark-blue);
            transition: border-color var(--transition);
        }

        /* Input Focus State */
        #form form input:focus {
            border-color: var(--green);
            outline: none;
        }

        /* Form Labels */
        .input-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 1rem;
            color: var(--dark-blue);
        }

        /* Help Text */
        .input-group .help {
            opacity: 0.7;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        /* Actions Group */
        .input-group.actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Action Buttons */
        .input-group.actions .button,
        .input-group.actions button {
            width: 48%;
            padding: 0.8rem 1.2rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: var(--card-border-radius);
            transition: background-color var(--transition), box-shadow var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Save Button */
        .input-group.actions button {
            background-color: var(--green);
            color: var(--white);
            border: none;
        }

        /* Cancel Button */
        .input-group.actions .button.cancel {
            background-color: var(--white);
            color: var(--red);
            border: 2px solid var(--red);
        }

        /* Hover and Active States for Buttons */
        .input-group.actions button:hover,
        .input-group.actions .button.cancel:hover {
            opacity: 0.9;
        }

        .input-group.actions button:active,
        .input-group.actions .button.cancel:active {
            opacity: 0.8;
        }

        .input-group.actions button:focus,
        .input-group.actions .button.cancel:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.3);
        }

        /* Remove Button */
        .button.cancel {
            background-color: var(--white);
            color: var(--red);
            border: 2px solid var(--red);
            padding: 0.6rem 1.2rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: var(--card-border-radius);
            text-transform: uppercase;
            cursor: pointer;
            transition: background-color var(--transition), color var(--transition), border-color var(--transition), box-shadow var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .button.cancel:hover {
            background-color: var(--red);
            color: var(--white);
        }

        .button.cancel:active {
            background-color: darkred;
        }

        .button.cancel:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>

<body>
    <header>
        <div id="logo">
            <b>FINASE</b>
            <img src="./assets/logo1.svg" alt="Finance Logo" aria-label="Dev Finance Logo" width="50px">
        </div>
        <a href="invest.php" class="button">Investing Options</a>
        <a href="extract_bill.php" class="button">Extract Bill</a>
        <a href="login.php" class="logout-button">Logout</a>
    </header>

    <main class="container">
        <section id="balance">
            <h2 class="sr-only">Balance Overview</h2>

            <div class="card">
                <h3>
                    <span>Income</span>
                    <img src="./assets/income.svg" alt="Income icon">
                </h3>
                <p id="incomeDisplay">Rs 0.00</p>
            </div>

            <div class="card">
                <h3>
                    <span>Expenses</span>
                    <img src="./assets/expense.svg" alt="Expenses icon">
                </h3>
                <p id="expenseDisplay">Rs 0.00</p>
            </div>

            <div class="card total">
                <h3>
                    <span>Total</span>
                    <img src="./assets/total.svg" alt="Total icon">
                </h3>
                <p id="totalDisplay">Rs 0.00</p>
            </div>
        </section>

        <section id="transaction">
            <h2 class="sr-only">Transaction History</h2>

            <a href="#" onclick="Modal.open()" class="button">New Transaction</a>

            <table id="data-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $index => $transaction) : ?>
                        <?php
                        $amountClass = $transaction['amount'] > 0 ? 'income' : 'expense';
                        $amountFormatted = number_format($transaction['amount'], 2, '.', ',');
                        ?>
                        <tr>
                            <td class="description"><?php echo htmlspecialchars($transaction['description']); ?></td>
                            <td class="<?php echo $amountClass; ?>">Rs <?php echo $amountFormatted; ?></td>
                            <td class="date"><?php echo date('d/m/Y', strtotime($transaction['date'])); ?></td>
                            <td>
                                <a href="?delete=<?php echo $transaction['id']; ?>" class="button cancel">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    <div class="modal-overlay">
        <div class="modal">
            <div id="form">
                <h2>New Transaction</h2>
                <form action="" method="POST">
                    <div class="input-group">
                        <label for="description">Description</label>
                        <input type="text" id="description" name="description" placeholder="Description" required>
                    </div>

                    <div class="input-group">
                        <label for="amount">Amount</label>
                        <input type="number" step="0.01" id="amount" name="amount" placeholder="0.00" required>
                        <small class="help">Use a negative sign (-) for expenses and a fullstop (.) for decimal places.</small>
                    </div>

                    <div class="input-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" required>
                    </div>

                    <div class="input-group actions">
                        <a href="#" class="button cancel" onclick="Modal.close()">Cancel</a>
                        <button type="submit">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Finase</p>
    </footer>

    <script>
        // Modal functionality to handle opening and closing of the modal
        const Modal = {
            open() {
                document.querySelector('.modal-overlay').classList.add('active');
                document.body.classList.add('no-scroll'); // Prevent scrolling
            },
            close() {
                document.querySelector('.modal-overlay').classList.remove('active');
                document.body.classList.remove('no-scroll'); // Allow scrolling
            }
        };

        // Update the balance displays
        function updateBalance() {
            let income = 0;
            let expenses = 0;

            document.querySelectorAll('#data-table tbody tr').forEach(row => {
                const amount = parseFloat(row.querySelector('td:nth-child(2)').textContent.replace('Rs ', '').replace(/,/g, '').replace('.', '.'));
                if (amount > 0) {
                    income += amount;
                } else {
                    expenses += amount;
                }
            });

            const total = income + expenses;

            document.getElementById('incomeDisplay').innerText = 'Rs ' + numberWithCommas(income.toFixed(2));
            document.getElementById('expenseDisplay').innerText = 'Rs ' + numberWithCommas(expenses.toFixed(2));
            document.getElementById('totalDisplay').innerText = 'Rs ' + numberWithCommas(total.toFixed(2));

        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateBalance();
        });
    </script>
</body>

</html>