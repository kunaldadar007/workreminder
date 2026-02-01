<?php
/**
 * Admin Users Management Page
 * Work Reminder and Chat Bot System
 * B.Sc Computer Science Final Year Project
 */

require_once '../config/config.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Handle user actions
$action = $_GET['action'] ?? 'view';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            // Don't allow deletion of admin users
            $check_query = "SELECT role FROM users WHERE id = :user_id";
            $check_stmt = $db->prepare($check_query);
            $check_stmt->bindParam(':user_id', $user_id);
            $check_stmt->execute();
            $user_role = $check_stmt->fetch(PDO::FETCH_ASSOC)['role'];
            
            if ($user_role === 'admin') {
                $error = "Cannot delete admin users";
            } else {
                // Delete user (cascade will delete their tasks)
                $delete_query = "DELETE FROM users WHERE id = :user_id AND role != 'admin'";
                $delete_stmt = $db->prepare($delete_query);
                $delete_stmt->bindParam(':user_id', $user_id);
                
                if ($delete_stmt->execute()) {
                    $message = "User deleted successfully";
                } else {
                    $error = "Failed to delete user";
                }
            }
        } catch(PDOException $exception) {
            $error = "Database error: " . $exception->getMessage();
        }
    }
    
    if (isset($_POST['toggle_status'])) {
        $user_id = $_POST['user_id'];
        $is_active = $_POST['is_active'];
        
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            // Don't allow deactivating admin users
            $check_query = "SELECT role FROM users WHERE id = :user_id";
            $check_stmt = $db->prepare($check_query);
            $check_stmt->bindParam(':user_id', $user_id);
            $check_stmt->execute();
            $user_role = $check_stmt->fetch(PDO::FETCH_ASSOC)['role'];
            
            if ($user_role === 'admin') {
                $error = "Cannot modify admin user status";
            } else {
                $new_status = $is_active ? 0 : 1;
                $update_query = "UPDATE users SET is_active = :is_active WHERE id = :user_id AND role != 'admin'";
                $update_stmt = $db->prepare($update_query);
                $update_stmt->bindParam(':is_active', $new_status);
                $update_stmt->bindParam(':user_id', $user_id);
                
                if ($update_stmt->execute()) {
                    $message = "User status updated successfully";
                } else {
                    $error = "Failed to update user status";
                }
            }
        } catch(PDOException $exception) {
            $error = "Database error: " . $exception->getMessage();
        }
    }
}

// Get users with statistics
try {
    $database = new Database();
    $db = $database->getConnection();
    
    $users_query = "SELECT u.id, u.username, u.email, u.full_name, u.role, u.is_active, u.created_at,
                   COUNT(t.id) as task_count,
                   SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END) as completed_tasks
                   FROM users u 
                   LEFT JOIN tasks t ON u.id = t.user_id 
                   GROUP BY u.id 
                   ORDER BY u.created_at DESC";
    
    $users_stmt = $db->prepare($users_query);
    $users_stmt->execute();
    $users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $exception) {
    $users = [];
    $error = "Database error: " . $exception->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Panel</title>
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
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="users.php" class="nav-link active">Users</a>
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
            <a href="dashboard.php" class="sidebar-item">
                📊 Dashboard
            </a>
            <a href="users.php" class="sidebar-item active">
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
        <!-- Header Section -->
        <div class="page-header">
            <div class="glass-container page-header-content">
                <h1>Manage Users</h1>
                <p>View and manage all system users</p>
            </div>
        </div>

        <!-- Messages -->
        <?php if ($message): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Users Table -->
        <section class="users-section">
            <div class="glass-container users-card">
                <div class="table-header">
                    <h3>All Users</h3>
                    <div class="table-actions">
                        <input type="text" id="searchUsers" placeholder="Search users..." class="form-control">
                        <button class="btn btn-primary" onclick="exportUsers()">Export CSV</button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Tasks</th>
                                <th>Completed</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="10" class="text-center">No users found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                    <tr class="user-row" data-username="<?php echo strtolower($user['username']); ?>" data-email="<?php echo strtolower($user['email']); ?>">
                                        <td><?php echo $user['id']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                            <?php if ($user['role'] === 'admin'): ?>
                                                <span class="badge badge-admin">ADMIN</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $user['role']; ?>">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $user['task_count']; ?></td>
                                        <td><?php echo $user['completed_tasks']; ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $user['is_active'] ? 'active' : 'inactive'; ?>">
                                                <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo formatDate($user['created_at']); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <?php if ($user['role'] !== 'admin'): ?>
                                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to toggle this user\'s status?')">
                                                        <input type="hidden" name="toggle_status" value="1">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                        <input type="hidden" name="is_active" value="<?php echo $user['is_active']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-<?php echo $user['is_active'] ? 'warning' : 'success'; ?>">
                                                            <?php echo $user['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                                        </button>
                                                    </form>
                                                    
                                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user? All their tasks will also be deleted.')">
                                                        <input type="hidden" name="delete_user" value="1">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="text-muted">Protected</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Statistics Section -->
        <section class="user-stats-section">
            <div class="stats-grid">
                <div class="stat-card glass-container">
                    <div class="stat-number"><?php echo count($users); ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-card glass-container">
                    <div class="stat-number"><?php echo count(array_filter($users, fn($u) => $u['is_active'])); ?></div>
                    <div class="stat-label">Active Users</div>
                </div>
                <div class="stat-card glass-container">
                    <div class="stat-number"><?php echo count(array_filter($users, fn($u) => $u['role'] === 'admin')); ?></div>
                    <div class="stat-label">Admin Users</div>
                </div>
                <div class="stat-card glass-container">
                    <div class="stat-number"><?php echo array_sum(array_column($users, 'task_count')); ?></div>
                    <div class="stat-label">Total Tasks</div>
                </div>
            </div>
        </section>
    </main>

    <style>
        /* Users management specific styles */
        .page-header {
            margin-bottom: 2rem;
        }

        .page-header-content {
            padding: 2rem;
            text-align: center;
        }

        .page-header-content h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .users-section {
            margin-bottom: 2rem;
        }

        .users-card {
            padding: 1.5rem;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .table-header h3 {
            font-size: 1.3rem;
            color: var(--text-primary);
        }

        .table-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .table-actions .form-control {
            width: 250px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--surface-color);
            border-radius: 10px;
            overflow: hidden;
        }

        .users-table th,
        .users-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--glass-border);
        }

        .users-table th {
            background: var(--glass-border);
            font-weight: 600;
            color: var(--text-primary);
        }

        .users-table tr:hover {
            background: var(--glass-bg);
        }

        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        .badge-admin {
            background: var(--error-color);
            color: var(--text-primary);
        }

        .badge-user {
            background: var(--success-color);
            color: var(--text-primary);
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-active {
            background: rgba(70, 211, 105, 0.2);
            color: var(--success-color);
        }

        .status-inactive {
            background: rgba(232, 124, 3, 0.2);
            color: var(--error-color);
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .action-buttons form {
            margin: 0;
        }

        .text-muted {
            color: var(--text-secondary);
            font-style: italic;
        }

        .text-center {
            text-align: center;
            color: var(--text-secondary);
        }

        .user-stats-section {
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .table-header {
                flex-direction: column;
                align-items: stretch;
            }
            
            .table-actions {
                flex-direction: column;
            }
            
            .table-actions .form-control {
                width: 100%;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .users-table {
                font-size: 0.875rem;
            }
            
            .users-table th,
            .users-table td {
                padding: 0.5rem;
            }
        }
    </style>

    <script src="../assets/js/app.js"></script>
    <script>
        // Search functionality
        document.getElementById('searchUsers').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#usersTableBody .user-row');
            
            rows.forEach(row => {
                const username = row.dataset.username;
                const email = row.dataset.email;
                
                if (username.includes(searchTerm) || email.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Export users to CSV
        function exportUsers() {
            const rows = document.querySelectorAll('#usersTableBody .user-row');
            let csv = 'ID,Username,Full Name,Email,Role,Tasks,Completed,Status,Joined\n';
            
            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    const cells = row.querySelectorAll('td');
                    const rowData = [
                        cells[0].textContent.trim(),
                        cells[1].textContent.trim(),
                        cells[2].textContent.trim(),
                        cells[3].textContent.trim(),
                        cells[4].textContent.trim(),
                        cells[5].textContent.trim(),
                        cells[6].textContent.trim(),
                        cells[7].textContent.trim(),
                        cells[8].textContent.trim()
                    ];
                    csv += rowData.map(cell => `"${cell}"`).join(',') + '\n';
                }
            });
            
            // Create download link
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'users_export_' + new Date().toISOString().split('T')[0] + '.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>
