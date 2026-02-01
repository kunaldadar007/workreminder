<?php
/**
 * Login Page
 * Work Reminder and Chat Bot System
 * B.Sc Computer Science Final Year Project
 */

require_once 'config/config.php';

// Redirect to dashboard if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Username or email is required";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    }
    
    if (empty($errors)) {
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            // Query user by username or email
            $query = "SELECT id, username, email, password, full_name, role FROM users WHERE (username = :username OR email = :username) AND is_active = 1";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['user_role'] = $user['role'];
                    $_SESSION['login_time'] = time();
                    
                    // Redirect based on role
                    if ($user['role'] === 'admin') {
                        redirect('admin/dashboard.php');
                    } else {
                        redirect('dashboard.php');
                    }
                } else {
                    $errors[] = "Invalid password";
                }
            } else {
                $errors[] = "User not found or account is inactive";
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
    <title>Login - WorkFlow</title>
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
                    <a href="login.php" class="nav-link active">Login</a>
                    <a href="register.php" class="nav-link">Register</a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
                <div class="auth-container">
                    <div class="card auth-card">
                        <div class="auth-header">
                            <h1>Welcome Back</h1>
                            <p>Sign in to your account to manage your tasks</p>
                        </div>

                        <!-- Success Message -->
                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success">
                                <?php 
                                echo $_SESSION['success_message'];
                                unset($_SESSION['success_message']);
                                ?>
                            </div>
                        <?php endif; ?>

                        <!-- Error Messages -->
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-error">
                                <?php foreach ($errors as $error): ?>
                                    <p><?php echo $error; ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Login Form -->
                        <form id="loginForm" method="POST" class="auth-form">
                            <div class="form-group">
                                <label for="username" class="form-label">Username or Email</label>
                                <input type="text" id="username" name="username" class="form-control" 
                                       placeholder="Enter your username or email" required
                                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                            </div>

                            <div class="form-group">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" id="password" name="password" class="form-control" 
                                       placeholder="Enter your password" required>
                            </div>

                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="remember" id="remember">
                                    <span class="checkmark"></span>
                                    Remember me
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary btn-full">
                                Sign In
                            </button>
                        </form>

                        <div class="auth-footer">
                            <p>Don't have an account? <a href="register.php">Sign up</a></p>
                            <p><a href="forgot_password.php">Forgot password?</a></p>
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

        .checkbox-label {
            display: flex;
            align-items: center;
            cursor: pointer;
            color: var(--text-secondary);
            font-size: 0.875rem;
            font-weight: 500;
            gap: var(--space-3);
        }

        .checkbox-label input[type="checkbox"] {
            width: auto;
            margin: 0;
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
            margin-bottom: var(--space-2);
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
