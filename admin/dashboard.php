<?php
/**
 * Admin Dashboard Page
 * Work Reminder and Chat Bot System
 * B.Sc Computer Science Final Year Project
 */

require_once '../config/config.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Get admin statistics
try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Total users
    $users_query = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
    $users_stmt = $db->prepare($users_query);
    $users_stmt->execute();
    $total_users = $users_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Active users (logged in within last 24 hours)
    $active_query = "SELECT COUNT(*) as active FROM users WHERE role = 'user' AND is_active = 1";
    $active_stmt = $db->prepare($active_query);
    $active_stmt->execute();
    $active_users = $active_stmt->fetch(PDO::FETCH_ASSOC)['active'];
    
    // Total tasks
    $tasks_query = "SELECT COUNT(*) as total FROM tasks";
    $tasks_stmt = $db->prepare($tasks_query);
    $tasks_stmt->execute();
    $total_tasks = $tasks_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Completed tasks
    $completed_query = "SELECT COUNT(*) as completed FROM tasks WHERE status = 'completed'";
    $completed_stmt = $db->prepare($completed_query);
    $completed_stmt->execute();
    $completed_tasks = $completed_stmt->fetch(PDO::FETCH_ASSOC)['completed'];
    
    // Recent users
    $recent_users_query = "SELECT username, email, created_at FROM users WHERE role = 'user' ORDER BY created_at DESC LIMIT 5";
    $recent_users_stmt = $db->prepare($recent_users_query);
    $recent_users_stmt->execute();
    $recent_users = $recent_users_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Recent tasks
    $recent_tasks_query = "SELECT t.title, t.task_date, t.task_time, u.username FROM tasks t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC LIMIT 5";
    $recent_tasks_stmt = $db->prepare($recent_tasks_query);
    $recent_tasks_stmt->execute();
    $recent_tasks = $recent_tasks_stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $exception) {
    $total_users = $active_users = $total_tasks = $completed_tasks = 0;
    $recent_users = $recent_tasks = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" href="../assets/images/favicon.ico">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="navbar-content">
            <a href="dashboard.php" class="logo">
                🛡️ Admin Panel
            </a>
            <div class="nav-links">
                <a href="dashboard.php" class="nav-link active">Dashboard</a>
                <a href="users.php" class="nav-link">Users</a>
                <a href="tasks.php" class="nav-link">Tasks</a>
                <a href="logs.php" class="nav-link">Logs</a>
                <a href="../dashboard.php" class="nav-link">User View</a>
                <div class="user-menu">
                    <span class="user-name">Admin: <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                    <a href="../api/logout.php" class="btn btn-secondary btn-sm">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h3>Admin Menu</h3>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="sidebar-item active">
                📊 Dashboard
            </a>
            <a href="users.php" class="sidebar-item">
                👥 Manage Users
            </a>
            <a href="tasks.php" class="sidebar-item">
                📝 Manage Tasks
            </a>
            <a href="logs.php" class="sidebar-item">
                📋 View Logs
            </a>
            <a href="settings.php" class="sidebar-item">
                ⚙️ Settings
            </a>
            <a href="../dashboard.php" class="sidebar-item">
                👤 User View
            </a>
            <a href="../api/logout.php" class="sidebar-item">
                🚪 Logout
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="glass-container welcome-card">
                <h1>Admin Dashboard</h1>
                <p>System overview and management panel</p>
            </div>
        </div>

        <!-- Statistics Section -->
        <section class="stats-section">
            <div class="stats-grid">
                <div class="stat-card glass-container">
                    <div class="stat-number"><?php echo $total_users; ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-card glass-container">
                    <div class="stat-number"><?php echo $active_users; ?></div>
                    <div class="stat-label">Active Users</div>
                </div>
                <div class="stat-card glass-container">
                    <div class="stat-number"><?php echo $total_tasks; ?></div>
                    <div class="stat-label">Total Tasks</div>
                </div>
                <div class="stat-card glass-container">
                    <div class="stat-number"><?php echo $completed_tasks; ?></div>
                    <div class="stat-label">Completed Tasks</div>
                </div>
            </div>
        </section>

        <!-- Recent Activity Section -->
        <div class="activity-section">
            <div class="activity-grid">
                <!-- Recent Users -->
                <div class="glass-container activity-card">
                    <div class="card-header">
                        <h3>Recent Users</h3>
                        <a href="users.php" class="btn btn-sm btn-secondary">View All</a>
                    </div>
                    <div class="activity-list">
                        <?php if (empty($recent_users)): ?>
                            <p class="text-center">No recent users</p>
                        <?php else: ?>
                            <?php foreach ($recent_users as $user): ?>
                                <div class="activity-item">
                                    <div class="activity-info">
                                        <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                        <span class="activity-meta"><?php echo htmlspecialchars($user['email']); ?></span>
                                    </div>
                                    <div class="activity-time">
                                        <?php echo formatDate($user['created_at']); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Tasks -->
                <div class="glass-container activity-card">
                    <div class="card-header">
                        <h3>Recent Tasks</h3>
                        <a href="tasks.php" class="btn btn-sm btn-secondary">View All</a>
                    </div>
                    <div class="activity-list">
                        <?php if (empty($recent_tasks)): ?>
                            <p class="text-center">No recent tasks</p>
                        <?php else: ?>
                            <?php foreach ($recent_tasks as $task): ?>
                                <div class="activity-item">
                                    <div class="activity-info">
                                        <strong><?php echo htmlspecialchars($task['title']); ?></strong>
                                        <span class="activity-meta">by <?php echo htmlspecialchars($task['username']); ?></span>
                                    </div>
                                    <div class="activity-time">
                                        <?php echo formatDate($task['task_date']); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Section -->
        <section class="quick-actions-section">
            <div class="glass-container quick-actions-card">
                <h3>Quick Actions</h3>
                <div class="quick-actions-grid">
                    <a href="users.php?action=add" class="btn btn-primary">
                        ➕ Add User
                    </a>
                    <a href="tasks.php?action=view" class="btn btn-secondary">
                        📋 View All Tasks
                    </a>
                    <a href="logs.php" class="btn btn-secondary">
                        📊 View System Logs
                    </a>
                    <a href="settings.php" class="btn btn-secondary">
                        ⚙️ System Settings
                    </a>
                    <a href="backup.php" class="btn btn-warning">
                        💾 Backup Database
                    </a>
                    <a href="reports.php" class="btn btn-success">
                        📈 Generate Reports
                    </a>
                </div>
            </div>
        </section>
    </main>

    <style>
        /* Admin specific styles */
        .welcome-section {
            margin-bottom: 2rem;
        }

        .welcome-card {
            padding: 2rem;
            text-align: center;
        }

        .welcome-card h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .activity-section {
            margin-bottom: 2rem;
        }

        .activity-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
        }

        .activity-card {
            padding: 1.5rem;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .card-header h3 {
            font-size: 1.2rem;
            color: var(--text-primary);
        }

        .activity-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .activity-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            background: var(--surface-color);
            border-radius: 10px;
            transition: var(--transition);
        }

        .activity-item:hover {
            background: var(--glass-border);
            transform: translateX(5px);
        }

        .activity-info strong {
            display: block;
            color: var(--text-primary);
        }

        .activity-meta {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .activity-time {
            font-size: 0.875rem;
            color: var(--text-secondary);
            text-align: right;
        }

        .quick-actions-section {
            margin-bottom: 2rem;
        }

        .quick-actions-card {
            padding: 1.5rem;
        }

        .quick-actions-card h3 {
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .quick-actions-grid .btn {
            text-align: center;
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .text-center {
            text-align: center;
            color: var(--text-secondary);
        }

        @media (max-width: 768px) {
            .activity-grid {
                grid-template-columns: 1fr;
            }
            
            .quick-actions-grid {
                grid-template-columns: 1fr;
            }
            
            .activity-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .activity-time {
                text-align: left;
            }
        }
    </style>

    <script src="../assets/js/app.js"></script>
</body>
</html>
