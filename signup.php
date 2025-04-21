<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // Database configuration (use environment variables or config files)
    $servername = getenv('DB_SERVER') ?: "localhost";
    $username = getenv('DB_USER') ?: "root";
    $password = getenv('DB_PASS') ?: "Aanthony912268@";
    $dbname = getenv('DB_NAME') ?: "mydatabase";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit();
    }

    // Get form data and sanitize input
    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));
    $confirmPassword = trim(filter_input(INPUT_POST, 'confirm-password', FILTER_SANITIZE_STRING));

    // Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }

    if ($password !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        exit();
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit();
    }

    // Check if email already exists
    $sql = "SELECT id FROM users1 WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'SQL prepare failed']);
        $conn->close();
        exit();
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        $stmt->close();
        $conn->close();
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert user into database
    $sql = "INSERT INTO users1 (firstname, lastname, email, password) VALUES (?, '', ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'SQL prepare failed']);
        $conn->close();
        exit();
    }

    $stmt->bind_param("sss", $name, $email, $hashed_password);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User registered successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Sign up failed: ' . $stmt->error]);
    }

    // Close connections
    $stmt->close();
    $conn->close();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>
    <style>
        /* Global Styles */
        :root {
            --primary-green: #4CAF50;
            --dark-green: #388E3C;
            --light-green: #C8E6C9;
            --white: #ffffff;
            --light-gray: #f0f2f5;
            --dark-gray: #333;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            font-size: 93.75%;
            /* 15 px */
        }

        body {
            background: var(--light-gray);
            font-family: 'Poppins', sans-serif;
            color: var(--dark-gray);
            line-height: 1.4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            max-width: 800px;
            margin: auto;
            padding: 1.5rem;
            background: var(--white);
            border-radius: 0.75rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--light-green);
        }

        .title {
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-green);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            color: var(--dark-gray);
        }

        .form-group input {
            width: 100%;
            padding: 0.6rem;
            border: 1px solid var(--light-gray);
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 6px rgba(76, 175, 80, 0.3);
            outline: none;
        }

        button {
            display: inline-block;
            width: 100%;
            height: 45px;
            border: none;
            color: var(--white);
            background: var(--primary-green);
            border-radius: 0.5rem;
            cursor: pointer;
            line-height: 45px;
            text-align: center;
            font-size: 0.875rem;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        button:hover,
        button:focus {
            background: var(--dark-green);
            transform: scale(1.03);
        }

        button:disabled {
            background: var(--light-gray);
            cursor: not-allowed;
        }

        .connect-title {
            text-align: center;
            margin: 1rem 0 0.5rem;
            font-size: 1rem;
            font-weight: 500;
            color: var(--dark-gray);
        }

        .social-links {
            text-align: center;
            margin-bottom: 1rem;
        }

        .social-links a {
            color: var(--dark-gray);
            font-size: 1.25rem;
            margin: 0 0.5rem;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .social-links a:hover {
            color: var(--primary-green);
        }

        .login-redirect {
            text-align: center;
        }

        .login-redirect a {
            color: var(--primary-green);
            text-decoration: none;
            font-weight: 500;
        }

        .login-redirect a:hover {
            text-decoration: underline;
        }

        .form-message {
            margin-top: 1rem;
            text-align: center;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .form-message.success {
            color: var(--primary-green);
        }

        .form-message.error {
            color: var(--dark-green);
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <main class="container">
        <h1 class="title">Sign Up</h1>
        <form id="signup-form" method="post">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="form-group">
                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password" required>
            </div>
            <div class="form-group">
                <button type="submit" id="submit-button">Sign Up</button>
            </div>
            <div id="form-message" class="form-message"></div>
        </form>

        <h3 class="connect-title">Connect with us</h3>
        <div class="social-links">
            <a href="#" class="fa fa-facebook" aria-label="Facebook"></a>
            <a href="#" class="fa fa-twitter" aria-label="Twitter"></a>
            <a href="#" class="fa fa-google" aria-label="Google"></a>
            <a href="#" class="fa fa-linkedin" aria-label="LinkedIn"></a>
        </div>

        <p class="login-redirect">
            Already have an account? <a href="login.php">Login here</a>
        </p>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('signup-form');
            const messageDiv = document.getElementById('form-message');
            const submitButton = document.getElementById('submit-button');

            form.addEventListener('submit', async function(event) {
                event.preventDefault();
                messageDiv.textContent = '';

                const name = document.getElementById('name').value.trim();
                const email = document.getElementById('email').value.trim();
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm-password').value;

                // Basic client-side validation
                if (!name || !email || !password || !confirmPassword) {
                    messageDiv.textContent = 'Please fill in all fields.';
                    messageDiv.style.color = 'red';
                    return;
                }

                if (password !== confirmPassword) {
                    messageDiv.textContent = 'Passwords do not match.';
                    messageDiv.style.color = 'red';
                    return;
                }

                // Disable the submit button
                submitButton.disabled = true;

                try {
                    // Prepare data for sending
                    const formData = new FormData(form);

                    // Send data to server via AJAX
                    const response = await fetch('', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        messageDiv.textContent = 'Sign up successful! Redirecting to login...';
                        messageDiv.style.color = 'green';
                        setTimeout(() => {
                            window.location.href = 'login.php'; // Redirect to login page
                        }, 2000);
                    } else {
                        messageDiv.textContent = 'Sign up failed: ' + data.message;
                        messageDiv.style.color = 'red';
                        // Re-enable the submit button
                        submitButton.disabled = false;
                    }
                } catch (error) {
                    console.error('Error:', error);
                    messageDiv.textContent = 'An error occurred. Please try again later.';
                    messageDiv.style.color = 'red';
                    // Re-enable the submit button
                    submitButton.disabled = false;
                }
            });
        });
    </script>
</body>

</html>