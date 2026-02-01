<?php
/**
 * Dashboard Page
 * Work Reminder and Chat Bot System
 * B.Sc Computer Science Final Year Project
 */

require_once 'config/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Get user information
$user_id = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];
$username = $_SESSION['username'];

// Get task statistics
try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Total tasks
    $total_query = "SELECT COUNT(*) as total FROM tasks WHERE user_id = :user_id";
    $total_stmt = $db->prepare($total_query);
    $total_stmt->bindParam(':user_id', $user_id);
    $total_stmt->execute();
    $total_tasks = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Completed tasks
    $completed_query = "SELECT COUNT(*) as completed FROM tasks WHERE user_id = :user_id AND status = 'completed'";
    $completed_stmt = $db->prepare($completed_query);
    $completed_stmt->bindParam(':user_id', $user_id);
    $completed_stmt->execute();
    $completed_tasks = $completed_stmt->fetch(PDO::FETCH_ASSOC)['completed'];
    
    // Pending tasks
    $pending_tasks = $total_tasks - $completed_tasks;
    
    // Today's tasks
    $today_query = "SELECT COUNT(*) as today FROM tasks WHERE user_id = :user_id AND task_date = CURDATE() AND status = 'pending'";
    $today_stmt = $db->prepare($today_query);
    $today_stmt->bindParam(':user_id', $user_id);
    $today_stmt->execute();
    $today_tasks = $today_stmt->fetch(PDO::FETCH_ASSOC)['today'];
    
} catch(PDOException $exception) {
    $total_tasks = $completed_tasks = $pending_tasks = $today_tasks = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="assets/images/favicon.ico">
</head>
<body>
    <div class="app-container">
        <!-- Navigation -->
        <nav class="navbar">
            <div class="navbar-content">
                <a href="dashboard.php" class="logo">
                    📋 WorkFlow
                </a>
                <div class="nav-links">
                    <a href="dashboard.php" class="nav-link active">Dashboard</a>
                    <a href="tasks.php" class="nav-link">Tasks</a>
                    <a href="calendar.php" class="nav-link">Calendar</a>
                    <a href="chatbot.php" class="nav-link">Chatbot</a>
                    <div class="user-menu">
                        <span class="user-name">Welcome, <?php echo htmlspecialchars($full_name); ?></span>
                        <a href="api/logout.php" class="btn btn-secondary btn-sm">Logout</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>Navigation</h3>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="sidebar-item active">
                    📊 Dashboard
                </a>
                <a href="tasks.php" class="sidebar-item">
                    📝 My Tasks
                </a>
                <a href="add_task.php" class="sidebar-item">
                    ➕ Add Task
                </a>
                <a href="calendar.php" class="sidebar-item">
                    📅 Calendar
                </a>
                <a href="chatbot.php" class="sidebar-item">
                    🤖 Chatbot
                </a>
                <a href="profile.php" class="sidebar-item">
                    👤 Profile
                </a>
                <a href="api/logout.php" class="sidebar-item">
                    🚪 Logout
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container">
        <!-- Welcome Section -->
                <div class="welcome-section">
                    <div class="welcome-card">
                        <h1>Welcome back, <?php echo htmlspecialchars($full_name); ?>! 👋</h1>
                        <p>Here's your task overview for today. Stay productive and keep up the great work!</p>
                    </div>
                </div>

                <!-- Statistics Section -->
                <section class="stats-section">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $total_tasks; ?></div>
                            <div class="stat-label">Total Tasks</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $completed_tasks; ?></div>
                            <div class="stat-label">Completed</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $pending_tasks; ?></div>
                            <div class="stat-label">Pending</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $today_tasks; ?></div>
                            <div class="stat-label">Today's Tasks</div>
                        </div>
                    </div>
                </section>

                <!-- Today's Tasks Section -->
                <section class="today-tasks-section">
                    <div class="section-header">
                        <h2 class="section-title">Today's Tasks</h2>
                        <div class="section-actions">
                            <button class="btn btn-primary" onclick="loadTodayTasks()">Refresh</button>
                            <a href="add_task.php" class="btn btn-secondary">Add Task</a>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div id="todayTasksContainer">
                            <div class="spinner"></div>
                        </div>
                    </div>
                </section>

                <!-- Calendar Preview Section -->
                <section class="calendar-preview-section">
                    <div class="section-header">
                        <h2 class="section-title">This Month</h2>
                        <a href="calendar.php" class="btn btn-secondary">View Full Calendar</a>
                    </div>
                    
                    <div class="card">
                        <div id="miniCalendar">
                            <div class="spinner"></div>
                        </div>
                    </div>
                </section>
            </div>
        </main>

    <!-- Chatbot Widget -->
    <div class="chatbot-container">
        <button class="chatbot-toggle" onclick="toggleChatbot()">
            
        </button>
        <div class="chatbot-window" id="chatbotWindow">
            <div class="chatbot-header">
                <h3>Task Assistant</h3>
                <button class="chatbot-close" onclick="toggleChatbot()">×</button>
            </div>
            <div class="chatbot-messages" id="chatbotMessages">
                <div class="message bot">Hello! I'm your task assistant. Ask me about your tasks!</div>
            </div>
            <div class="chatbot-input">
                <input type="text" id="chatbotInput" placeholder="Ask about your tasks..." onkeypress="handleChatbotKeypress(event)">
                <button class="chatbot-send" onclick="sendChatbotMessage()">Send</button>
            </div>
        </div>
    </div>

    <style>
        /* Dashboard specific styles */
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

        .stats-section {
            margin-bottom: 2rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-header h2 {
            font-size: 1.5rem;
            color: var(--text-primary);
        }

        .section-actions {
            display: flex;
            gap: 0.5rem;
        }

        .today-tasks-section,
        .calendar-preview-section {
            margin-bottom: var(--space-8);
        }

        .tasks-container,
        .calendar-container {
            min-height: 300px;
            padding: 1.5rem;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-name {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .sidebar-header {
            padding: 1rem;
            border-bottom: 1px solid var(--glass-border);
            margin-bottom: 1rem;
        }

        .sidebar-header h3 {
            font-size: 1.1rem;
            color: var(--text-primary);
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
        }

        /* Chatbot specific styles */
        .chatbot-header {
            padding: 1rem;
            background: var(--primary-color);
            color: var(--text-primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chatbot-header h3 {
            margin: 0;
            font-size: 1rem;
        }

        .chatbot-close {
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: var(--transition);
        }

        .chatbot-close:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .chatbot-messages {
            flex: 1;
            padding: 1rem;
            overflow-y: auto;
            max-height: 300px;
        }

        .chatbot-input {
            padding: 1rem;
            border-top: 1px solid var(--glass-border);
            display: flex;
            gap: 0.5rem;
        }

        .chatbot-input input {
            flex: 1;
            padding: 0.75rem;
            background: var(--surface-color);
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            color: var(--text-primary);
            font-size: 0.9rem;
        }

        .chatbot-send {
            padding: 0.75rem 1rem;
            background: var(--primary-color);
            color: var(--text-primary);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: var(--transition);
        }

        .chatbot-send:hover {
            background: #f40612;
        }

        .message {
            margin-bottom: 1rem;
            padding: 0.75rem;
            border-radius: 10px;
            max-width: 80%;
            word-wrap: break-word;
        }

        .message.user {
            background: var(--primary-color);
            color: var(--text-primary);
            margin-left: auto;
            text-align: right;
        }

        .message.bot {
            background: var(--surface-color);
            color: var(--text-primary);
        }

        @media (max-width: 768px) {
            .user-menu {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .section-actions {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>

    <script src="assets/js/app.js"></script>
    <script>
        // Load today's tasks on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadTodayTasks();
            loadMiniCalendar();
        });

        /**
         * Load today's tasks
         */
        async function loadTodayTasks() {
            const container = document.getElementById('todayTasksContainer');
            
            try {
                const response = await fetch('api/tasks.php?filter=today');
                const data = await response.json();
                
                if (data.success) {
                    renderTodayTasks(data.tasks);
                } else {
                    container.innerHTML = '<p class="text-center text-secondary">Error loading tasks.</p>';
                }
            } catch (error) {
                console.error('Error loading today\'s tasks:', error);
                container.innerHTML = '<p class="text-center text-secondary">Error loading tasks.</p>';
            }
        }

        /**
         * Render today's tasks
         */
        function renderTodayTasks(tasks) {
            const container = document.getElementById('todayTasksContainer');
            
            if (tasks.length === 0) {
                container.innerHTML = '<p class="text-center text-secondary">No tasks for today. Great job!</p>';
                return;
            }
            
            container.innerHTML = '';
            
            tasks.forEach(task => {
                const taskElement = createTaskElement(task);
                container.appendChild(taskElement);
            });
        }

        /**
         * Create task element
         */
        function createTaskElement(task) {
            const div = document.createElement('div');
            div.className = `task-item ${task.status === 'completed' ? 'completed' : ''}`;
            
            div.innerHTML = `
                <div class="task-header">
                    <h3 class="task-title">${task.title}</h3>
                    <span class="priority-badge priority-${task.priority}">${task.priority}</span>
                </div>
                <p class="task-description">${task.description || 'No description'}</p>
                <div class="task-meta">
                    <span><i class="icon-clock"></i> ${task.formatted_time}</span>
                    <span><i class="icon-tag"></i> ${task.task_type}</span>
                </div>
                <div class="task-actions">
                    ${task.status !== 'completed' ? `
                        <button class="btn btn-sm btn-success" onclick="completeTask(${task.id})">Complete</button>
                    ` : ''}
                    <button class="btn btn-sm btn-secondary" onclick="editTask(${task.id})">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteTask(${task.id})">Delete</button>
                </div>
            `;
            
            return div;
        }

        /**
         * Load mini calendar
         */
        async function loadMiniCalendar() {
            const container = document.getElementById('miniCalendar');
            
            try {
                const response = await fetch('api/tasks.php');
                const data = await response.json();
                
                if (data.success) {
                    renderMiniCalendar(data.tasks);
                } else {
                    container.innerHTML = '<p class="text-center text-secondary">Error loading calendar.</p>';
                }
            } catch (error) {
                console.error('Error loading calendar:', error);
                container.innerHTML = '<p class="text-center text-secondary">Error loading calendar.</p>';
            }
        }

        /**
         * Render mini calendar
         */
        function renderMiniCalendar(tasks) {
            const container = document.getElementById('miniCalendar');
            const today = new Date();
            const currentMonth = today.getMonth();
            const currentYear = today.getFullYear();
            
            // Create simple calendar view
            const calendarHTML = `
                <div class="mini-calendar">
                    <div class="calendar-header">
                        <h3>${today.toLocaleDateString('en-US', { month: 'long', year: 'numeric' })}</h3>
                    </div>
                    <div class="calendar-summary">
                        <p><strong>${tasks.filter(t => t.status === 'pending').length}</strong> tasks pending this month</p>
                        <p><strong>${tasks.filter(t => t.status === 'completed').length}</strong> tasks completed</p>
                    </div>
                </div>
            `;
            
            container.innerHTML = calendarHTML;
        }

        /**
         * Toggle chatbot window
         */
        function toggleChatbot() {
            const chatbotWindow = document.getElementById('chatbotWindow');
            chatbotWindow.classList.toggle('active');
        }

        /**
         * Handle chatbot input keypress
         */
        function handleChatbotKeypress(event) {
            if (event.key === 'Enter') {
                sendChatbotMessage();
            }
        }

        /**
         * Send chatbot message
         */
        async function sendChatbotMessage() {
            const input = document.getElementById('chatbotInput');
            const message = input.value.trim();
            
            if (!message) return;
            
            // Add user message
            addChatMessage('user', message);
            input.value = '';
            
            try {
                const response = await fetch('api/chatbot.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ query: message })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    addChatMessage('bot', data.response);
                } else {
                    addChatMessage('bot', 'Sorry, I encountered an error. Please try again.');
                }
            } catch (error) {
                console.error('Chatbot error:', error);
                addChatMessage('bot', 'Sorry, I\'m having trouble connecting. Please try again later.');
            }
        }

        /**
         * Add message to chatbot
         */
        function addChatMessage(type, message) {
            const messagesContainer = document.getElementById('chatbotMessages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}`;
            messageDiv.textContent = message;
            
            messagesContainer.appendChild(messageDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    </script>
</body>
</html>
