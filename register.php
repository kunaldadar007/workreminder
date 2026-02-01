<?php
/**
 * Registration Page
 * Work Reminder and Chat Bot System
 * B.Sc Computer Science Final Year Project
 */

require_once 'config/config.php';

// Redirect to dashboard if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $full_name = sanitizeInput($_POST['full_name']);
    
    // Basic validation
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Username is required";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters long";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long";
    }
    
    if (empty($full_name)) {
        $errors[] = "Full name is required";
    }
    
    // Check if passwords match
    if ($password !== $_POST['confirm_password']) {
        $errors[] = "Passwords do not match";
    }
    
    if (empty($errors)) {
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            // Check if username or email already exists
            $check_query = "SELECT id FROM users WHERE username = :username OR email = :email";
            $check_stmt = $db->prepare($check_query);
            $check_stmt->bindParam(':username', $username);
            $check_stmt->bindParam(':email', $email);
            $check_stmt->execute();
            
            if ($check_stmt->rowCount() > 0) {
                $errors[] = "Username or email already exists";
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert new user
                $insert_query = "INSERT INTO users (username, email, password, full_name) VALUES (:username, :email, :password, :full_name)";
                $insert_stmt = $db->prepare($insert_query);
                $insert_stmt->bindParam(':username', $username);
                $insert_stmt->bindParam(':email', $email);
                $insert_stmt->bindParam(':password', $hashed_password);
                $insert_stmt->bindParam(':full_name', $full_name);
                
                if ($insert_stmt->execute()) {
                    $_SESSION['success_message'] = "Registration successful! Please login.";
                    redirect('login.php');
                } else {
                    $errors[] = "Registration failed. Please try again.";
                }
            }
        } catch(PDOException $exception) {
            $errors[] = "Database error: " . $exception->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - WorkFlow</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="assets/images/favicon.ico">
</head>
<body>
    <div class="app-container">
        <!-- Navigation -->
        <nav class="navbar">
            <div class="navbar-content">
                <a href="index.php" class="logo">
                    📋 WorkFlow
                </a>
                <div class="nav-links">
                    <a href="index.php" class="nav-link">Home</a>
                    <a href="login.php" class="nav-link">Login</a>
                    <a href="register.php" class="nav-link active">Register</a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
                <div class="auth-container">
                    <div class="card auth-card">
                        <div class="auth-header">
                            <h1>Create Account</h1>
                            <p>Join us to manage your tasks efficiently</p>
                        </div>

                        <!-- Error Messages -->
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-error">
                                <?php foreach ($errors as $error): ?>
                                    <p><?php echo $error; ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Registration Form -->
                        <form id="registerForm" method="POST" class="auth-form">
                            <div class="form-group">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" id="full_name" name="full_name" class="form-control" 
                                       placeholder="Enter your full name" required
                                       value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" id="username" name="username" class="form-control" 
                                       placeholder="Choose a username" required
                                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" id="email" name="email" class="form-control" 
                                       placeholder="Enter your email" required
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" id="password" name="password" class="form-control" 
                                       placeholder="Create a password" required>
                                <small class="form-help">Minimum 6 characters</small>
                            </div>

                            <div class="form-group">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                                       placeholder="Confirm your password" required>
                            </div>

                            <button type="submit" class="btn btn-primary btn-full">
                                Create Account
                            </button>
                        </form>

                        <div class="auth-footer">
                            <p>Already have an account? <a href="login.php">Sign in</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <style>
        /* Authentication page styles */
        .auth-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - var(--navbar-height));
            padding: var(--space-8);
        }

        .auth-card {
            width: 100%;
            max-width: 450px;
            padding: var(--space-8);
        }

        .auth-header {
            text-align: center;
            margin-bottom: var(--space-8);
        }

        .auth-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: var(--space-2);
            color: var(--text-primary);
        }

        .auth-header p {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .auth-form {
            margin-bottom: var(--space-6);
        }

        .form-help {
            display: block;
            margin-top: var(--space-2);
            color: var(--text-tertiary);
            font-size: 0.75rem;
        }

        .btn-full {
            width: 100%;
            padding: var(--space-4);
            font-size: 1rem;
            font-weight: 600;
        }

        .auth-footer {
            text-align: center;
            padding-top: var(--space-6);
            border-top: 1px solid var(--border-light);
        }

        .auth-footer p {
            margin-bottom: 0;
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .auth-footer a {
            color: var(--primary-600);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition-fast);
        }

        .auth-footer a:hover {
            color: var(--primary-700);
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .auth-card {
                padding: var(--space-6);
            }
            
            .auth-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>

    <script src="assets/js/app.js"></script>
</body>
</html>
