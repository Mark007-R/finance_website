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

    // Get form data
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));

    // Basic validation
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit();
    }

    // Check if email exists
    $sql = "SELECT id, password FROM users1 WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'SQL prepare failed']);
        $conn->close();
        exit();
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Email not found']);
        $stmt->close();
        $conn->close();
        exit();
    }

    $user = $result->fetch_assoc();

    // Verify password
    if (!password_verify($password, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid password']);
        $stmt->close();
        $conn->close();
        exit();
    }

    // Login successful
    echo json_encode(['success' => true, 'message' => 'Login successful']);

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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="style/style2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        /* Global Styles */
        /* Global Styles */
        :root {
            --dark-blue: #363F5F;
            --green: #49aa26;
            --light-green: #3dd705;
            --white: #ffffff;
            --light-gray: #f0f2f5;
            --gray: #969cb3;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            overflow: hidden;
            /* Prevent scrolling */
        }

        body {
            background: var(--light-gray);
            font-family: 'Poppins', sans-serif;
            color: var(--dark-blue);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 500px;
            /* Adjusted for smaller width */
            padding: 1rem;
        }

        .form-container {
            background: var(--white);
            padding: 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid var(--light-gray);
        }

        .title {
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 1.5rem;
            /* Adjusted for smaller font size */
            font-weight: 700;
            color: var(--green);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            color: var(--dark-blue);
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input:focus {
            border-color: var(--green);
            box-shadow: 0 0 6px rgba(73, 170, 38, 0.3);
            outline: none;
        }

        button {
            display: inline-block;
            width: 100%;
            height: 40px;
            /* Adjusted height for better fit */
            border: none;
            color: var(--white);
            background: var(--green);
            border-radius: 0.5rem;
            cursor: pointer;
            line-height: 40px;
            text-align: center;
            font-size: 1rem;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        button:hover,
        button:focus {
            background: var(--light-green);
            transform: scale(1.03);
        }

        button:disabled {
            background: var(--gray);
            cursor: not-allowed;
        }

        .connect-title {
            text-align: center;
            margin: 1.5rem 0;
            font-size: 1rem;
            /* Adjusted for consistency */
            font-weight: 500;
            color: var(--dark-blue);
        }

        .social-icons {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .social-icons a {
            color: var(--black);
            font-size: 1.25rem;
            /* Adjusted size for better fit */
            margin: 0 0.5rem;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .social-icons a:hover {
            color: var(--green);
        }

        .signup-redirect {
            text-align: center;
        }

        .signup-redirect a {
            color: var(--green);
            text-decoration: none;
            font-weight: 500;
        }

        .signup-redirect a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <main class="container">
        <section class="login-section">
            <!-- <div class="image-container">
                <img src="img.jpg" alt="Login Image" class="login-img">
            </div> -->
            <div class="form-container">
                <h1 class="title">Login</h1>
                <form id="login-form">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <button type="submit">Login</button>
                </form>
                <h3 class="connect-title">Connect with</h3>
                <div class="social-icons">
                    <a href="#" class="fa fa-facebook" aria-label="Facebook"></a>
                    <a href="#" class="fa fa-twitter" aria-label="Twitter"></a>
                    <a href="#" class="fa fa-google" aria-label="Google"></a>
                    <a href="#" class="fa fa-linkedin" aria-label="LinkedIn"></a>
                </div>
                <p class="signup-redirect">
                    Don't have an account? <a href="signup.php">Sign Up</a>
                </p>
            </div>
        </section>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('login-form');
            const handleError = (message) => alert(message);

            form.addEventListener('submit', async function(event) {
                event.preventDefault();

                const email = document.getElementById('email').value.trim();
                const password = document.getElementById('password').value;

                // Basic client-side validation
                if (!email || !password) {
                    handleError('Please fill in all fields.');
                    return;
                }

                try {
                    // Prepare data for sending
                    const formData = new FormData(form);

                    // Send data to server via AJAX
                    const response = await fetch('', { // The form posts to itself
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert('Login successful!');
                        window.location.href = 'main.php'; // Redirect to index1 page
                    } else {
                        handleError('Login failed: ' + data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    handleError('An error occurred. Please try again later.');
                }
            });
        });
    </script>
</body>

</html>