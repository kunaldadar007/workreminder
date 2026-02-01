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
    <title>WorkFlow - Intelligent Task Management</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="assets/images/favicon.ico">
</head>
<body>
    <div class="app-container">
        <!-- Navigation -->
        <nav class="navbar">
            <div class="navbar-content">
                <a href="index.php" class="logo">
                    WorkFlow
                </a>
                <div class="nav-links">
                    <a href="index.php" class="nav-link active">Home</a>
                    <a href="login.php" class="nav-link">Login</a>
                    <a href="register.php" class="nav-link">Sign Up</a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
                <!-- Hero Section -->
                <section class="hero-section">
                    <div class="hero-content">
                        <h1 class="hero-title">
                            Intelligent Task Management for Modern Teams
                        </h1>
                        <p class="hero-subtitle">
                            Streamline your workflow with smart reminders, AI-powered assistance, and seamless collaboration. Stay productive and organized with WorkFlow.
                        </p>
                        <div class="hero-buttons">
                            <a href="register.php" class="btn btn-primary btn-lg">Get Started Free</a>
                            <a href="login.php" class="btn btn-secondary btn-lg">Sign In</a>
                        </div>
                    </div>
                </section>

                <!-- Features Section -->
                <section class="features-section">
                    <div class="section-header">
                        <h2 class="section-title">Powerful Features</h2>
                        <p class="section-subtitle">Everything you need to manage your tasks efficiently</p>
                    </div>
                    <div class="features-grid">
                        <div class="feature-card">
                            <div class="feature-icon">📅</div>
                            <h3>Smart Task Management</h3>
                            <p>Create, organize, and track your daily and monthly tasks with an intuitive interface designed for productivity.</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">🔔</div>
                            <h3>Intelligent Reminders</h3>
                            <p>Never miss a deadline with automated browser notifications and customizable alert systems.</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">🤖</div>
                            <h3>AI Assistant</h3>
                            <p>Get instant answers about your tasks and schedule with our intelligent chatbot powered by advanced NLP.</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">📊</div>
                            <h3>Analytics & Insights</h3>
                            <p>Track your productivity patterns with detailed statistics and actionable insights.</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">📱</div>
                            <h3>Responsive Design</h3>
                            <p>Access your tasks seamlessly across all devices with our mobile-optimized interface.</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">🔒</div>
                            <h3>Enterprise Security</h3>
                            <p>Your data is protected with bank-level encryption and secure authentication protocols.</p>
                        </div>
                    </div>
                </section>

                <!-- About Section -->
                <section class="about-section">
                    <div class="card about-content">
                        <h2>About WorkFlow</h2>
                        <p>
                            WorkFlow is a comprehensive task management system developed as a B.Sc Computer Science final year project. 
                            It combines cutting-edge web technologies with intelligent automation to help individuals and teams 
                            stay organized, productive, and focused on what matters most.
                        </p>
                        <div class="tech-stack">
                            <h3>Built with Modern Technology</h3>
                            <div class="tech-tags">
                                <span class="tech-tag">HTML5</span>
                                <span class="tech-tag">CSS3</span>
                                <span class="tech-tag">JavaScript ES6</span>
                                <span class="tech-tag">PHP</span>
                                <span class="tech-tag">MySQL</span>
                                <span class="tech-tag">Modern UI/UX</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- CTA Section -->
                <section class="cta-section">
                    <div class="card cta-content">
                        <h2>Ready to Transform Your Productivity?</h2>
                        <p>Join thousands of users who have already streamlined their workflow with WorkFlow.</p>
                        <div class="cta-buttons">
                            <a href="register.php" class="btn btn-primary btn-lg">Start Free Trial</a>
                            <a href="#features" class="btn btn-secondary btn-lg">Learn More</a>
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="footer-content">
                    <p>&copy; 2026 WorkFlow. B.Sc Computer Science Final Year Project.</p>
                </div>
            </div>
        </footer>
    </div>

    <style>
        /* Landing page specific styles */
        .hero-section {
            padding: var(--space-20) 0;
            text-align: center;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            padding: var(--space-12);
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: var(--space-6);
            color: var(--text-primary);
            line-height: 1.1;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: var(--text-secondary);
            margin-bottom: var(--space-8);
            line-height: 1.6;
        }

        .hero-buttons {
            display: flex;
            gap: var(--space-4);
            justify-content: center;
            flex-wrap: wrap;
        }

        .features-section {
            padding: var(--space-20) 0;
        }

        .section-header {
            text-align: center;
            margin-bottom: var(--space-12);
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: var(--space-4);
            color: var(--text-primary);
        }

        .section-subtitle {
            font-size: 1.125rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: var(--space-8);
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-2xl);
            padding: var(--space-8);
            text-align: center;
            transition: var(--transition-normal);
            box-shadow: var(--shadow-sm);
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: var(--space-4);
            display: block;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: var(--space-4);
            color: var(--primary-700);
        }

        .feature-card p {
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .about-section {
            padding: var(--space-20) 0;
        }

        .about-content {
            max-width: 800px;
            margin: 0 auto;
            padding: var(--space-8);
            text-align: center;
        }

        .about-content h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: var(--space-6);
            color: var(--primary-700);
        }

        .about-content p {
            font-size: 1.125rem;
            color: var(--text-secondary);
            line-height: 1.8;
            margin-bottom: var(--space-8);
        }

        .tech-stack h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: var(--space-4);
            color: var(--text-primary);
        }

        .tech-tags {
            display: flex;
            gap: var(--space-3);
            justify-content: center;
            flex-wrap: wrap;
        }

        .tech-tag {
            background: var(--primary-100);
            color: var(--primary-700);
            padding: var(--space-2) var(--space-4);
            border-radius: var(--radius-xl);
            font-size: 0.875rem;
            font-weight: 600;
        }

        .cta-section {
            padding: var(--space-20) 0;
        }

        .cta-content {
            max-width: 700px;
            margin: 0 auto;
            padding: var(--space-10);
            text-align: center;
            background: linear-gradient(135deg, var(--primary-600), var(--primary-700));
            color: var(--text-inverse);
            border: none;
        }

        .cta-content h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: var(--space-4);
        }

        .cta-content p {
            font-size: 1.125rem;
            opacity: 0.9;
            margin-bottom: var(--space-8);
        }

        .cta-buttons {
            display: flex;
            gap: var(--space-4);
            justify-content: center;
            flex-wrap: wrap;
        }

        .cta-content .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: var(--text-inverse);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .cta-content .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .footer {
            padding: var(--space-8) 0;
            background: var(--bg-tertiary);
            border-top: 1px solid var(--border-light);
        }

        .footer-content {
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.125rem;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
                gap: var(--space-6);
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .hero-buttons,
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-lg {
                width: 100%;
                max-width: 300px;
            }
        }

        @media (max-width: 480px) {
            .hero-content {
                padding: var(--space-6);
            }
            
            .hero-title {
                font-size: 2rem;
            }
            
            .feature-card {
                padding: var(--space-6);
            }
        }
    </style>

    <script src="assets/js/app.js"></script>
</body>
</html>
