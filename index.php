<?php
/**
 * Home Page - Landing Page
 * Work Reminder and Chat Bot System
 * B.Sc Computer Science Final Year Project
 */

require_once 'config/config.php';

// Redirect to dashboard if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Home</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="assets/images/favicon.ico">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="navbar-content">
            <a href="index.php" class="logo">
                📋 Work Reminder
            </a>
            <div class="nav-links">
                <a href="index.php" class="nav-link">Home</a>
                <a href="login.php" class="nav-link">Login</a>
                <a href="register.php" class="nav-link">Register</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="hero-section">
            <div class="glass-container hero-content">
                <h1 class="hero-title fade-in">
                    Welcome to Work Reminder & Chat Bot System
                </h1>
                <p class="hero-subtitle fade-in">
                    Your intelligent task management assistant with automated reminders and AI-powered chatbot support
                </p>
                <div class="hero-buttons fade-in">
                    <a href="register.php" class="btn btn-primary btn-large">Get Started</a>
                    <a href="login.php" class="btn btn-secondary btn-large">Login</a>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <section class="features-section">
            <h2 class="section-title">Features</h2>
            <div class="features-grid">
                <div class="feature-card glass-container fade-in">
                    <div class="feature-icon">📅</div>
                    <h3>Task Management</h3>
                    <p>Add, edit, delete, and track your daily and monthly tasks with ease</p>
                </div>
                <div class="feature-card glass-container fade-in">
                    <div class="feature-icon">🔔</div>
                    <h3>Smart Reminders</h3>
                    <p>Get browser notifications and sound alerts when your tasks are due</p>
                </div>
                <div class="feature-card glass-container fade-in">
                    <div class="feature-icon">🤖</div>
                    <h3>AI Chatbot</h3>
                    <p>Ask questions about your tasks and get instant responses</p>
                </div>
                <div class="feature-card glass-container fade-in">
                    <div class="feature-icon">📊</div>
                    <h3>Statistics</h3>
                    <p>Track your productivity with detailed task statistics and analytics</p>
                </div>
                <div class="feature-card glass-container fade-in">
                    <div class="feature-icon">📱</div>
                    <h3>Responsive Design</h3>
                    <p>Access your tasks from any device with our mobile-friendly interface</p>
                </div>
                <div class="feature-card glass-container fade-in">
                    <div class="feature-icon">🔒</div>
                    <h3>Secure</h3>
                    <p>Your data is protected with secure authentication and encryption</p>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section class="about-section">
            <div class="glass-container about-content">
                <h2>About This Project</h2>
                <p>
                    This Work Reminder and Chat Bot System is developed as a B.Sc Computer Science final year project.
                    It combines modern web technologies with intelligent task management to help users stay organized
                    and productive throughout their day.
                </p>
                <div class="tech-stack">
                    <h3>Technology Stack</h3>
                    <div class="tech-tags">
                        <span class="tech-tag">HTML5</span>
                        <span class="tech-tag">CSS3</span>
                        <span class="tech-tag">JavaScript ES6</span>
                        <span class="tech-tag">PHP</span>
                        <span class="tech-tag">MySQL</span>
                        <span class="tech-tag">Glassmorphism UI</span>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="glass-container footer-content">
            <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?> - B.Sc Computer Science Final Year Project</p>
        </div>
    </footer>

    <style>
        /* Additional styles for landing page */
        .hero-section {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .hero-content {
            text-align: center;
            max-width: 800px;
            padding: 3rem;
        }

        .hero-title {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, var(--primary-color), #ff6b6b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-large {
            padding: 1rem 2rem;
            font-size: 1.1rem;
        }

        .features-section {
            padding: 4rem 2rem;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            color: var(--text-primary);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            text-align: center;
            padding: 2rem;
            transition: var(--transition);
        }

        .feature-card:hover {
            transform: translateY(-10px);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .about-section {
            padding: 4rem 2rem;
        }

        .about-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            text-align: center;
        }

        .about-content h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .tech-stack {
            margin-top: 2rem;
        }

        .tech-stack h3 {
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .tech-tags {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .tech-tag {
            background: var(--primary-color);
            color: var(--text-primary);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .footer {
            padding: 2rem;
            text-align: center;
        }

        .footer-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 1rem;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</body>
</html>
