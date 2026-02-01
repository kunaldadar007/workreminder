/**
 * Main JavaScript Application
 * Work Reminder and Chat Bot System
 * B.Sc Computer Science Final Year Project
 */

// Global variables
let currentUser = null;
let tasks = [];
let reminders = [];
let notificationPermission = false;

// Initialize application
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

/**
 * Initialize the application
 */
function initializeApp() {
    // Request notification permission
    requestNotificationPermission();
    
    // Initialize sidebar toggle for mobile
    initializeSidebar();
    
    // Initialize chatbot
    initializeChatbot();
    
    // Load user data
    loadUserData();
    
    // Start reminder checker
    startReminderChecker();
    
    // Initialize form validators
    initializeFormValidators();
}

/**
 * Request browser notification permission
 */
function requestNotificationPermission() {
    if ('Notification' in window) {
        Notification.requestPermission().then(permission => {
            notificationPermission = permission === 'granted';
            console.log('Notification permission:', permission);
        });
    }
}

/**
 * Initialize mobile sidebar toggle
 */
function initializeSidebar() {
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
}

/**
 * Initialize chatbot functionality
 */
function initializeChatbot() {
    const chatbotToggle = document.querySelector('.chatbot-toggle');
    const chatbotWindow = document.querySelector('.chatbot-window');
    const chatbotInput = document.querySelector('.chatbot-input input');
    const chatbotSend = document.querySelector('.chatbot-send');
    
    // Toggle chatbot window
    if (chatbotToggle && chatbotWindow) {
        chatbotToggle.addEventListener('click', function() {
            chatbotWindow.classList.toggle('active');
            if (chatbotWindow.classList.contains('active')) {
                chatbotInput.focus();
            }
        });
    }
    
    // Send message
    function sendMessage() {
        const message = chatbotInput.value.trim();
        if (message) {
            addChatMessage('user', message);
            chatbotInput.value = '';
            
            // Send to server and get response
            sendChatbotQuery(message);
        }
    }
    
    if (chatbotSend) {
        chatbotSend.addEventListener('click', sendMessage);
    }
    
    if (chatbotInput) {
        chatbotInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    }
}

/**
 * Add message to chatbot window
 */
function addChatMessage(type, message) {
    const messagesContainer = document.querySelector('.chatbot-messages');
    if (!messagesContainer) return;
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = message;
    
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

/**
 * Send query to chatbot API
 */
async function sendChatbotQuery(query) {
    try {
        const response = await fetch('api/chatbot.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ query: query })
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
 * Load current user data
 */
function loadUserData() {
    // This would typically be loaded from session or API
    // For now, we'll use placeholder data
    currentUser = {
        id: 1,
        username: 'demo_user',
        fullName: 'Demo User'
    };
}

/**
 * Start reminder checker
 */
function startReminderChecker() {
    // Check for reminders every 30 seconds
    setInterval(checkReminders, 30000);
    
    // Also check immediately on load
    checkReminders();
}

/**
 * Check for pending reminders
 */
async function checkReminders() {
    try {
        const response = await fetch('api/check_reminders.php');
        const data = await response.json();
        
        if (data.success && data.reminders.length > 0) {
            data.reminders.forEach(reminder => {
                showNotification(reminder.title, reminder.description);
                playNotificationSound();
            });
        }
    } catch (error) {
        console.error('Reminder check error:', error);
    }
}

/**
 * Show browser notification
 */
function showNotification(title, body) {
    if (notificationPermission && 'Notification' in window) {
        const notification = new Notification(title, {
            body: body,
            icon: 'assets/images/notification-icon.png',
            badge: 'assets/images/badge-icon.png'
        });
        
        // Auto close after 5 seconds
        setTimeout(() => {
            notification.close();
        }, 5000);
        
        // Focus window when notification is clicked
        notification.onclick = function() {
            window.focus();
            notification.close();
        };
    }
}

/**
 * Play notification sound
 */
function playNotificationSound() {
    const audio = new Audio('assets/sounds/notification.mp3');
    audio.play().catch(error => {
        console.error('Sound play error:', error);
    });
}

/**
 * Initialize form validators
 */
function initializeFormValidators() {
    // Registration form
    const registerForm = document.querySelector('#registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', validateRegistration);
    }
    
    // Login form
    const loginForm = document.querySelector('#loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', validateLogin);
    }
    
    // Task form
    const taskForm = document.querySelector('#taskForm');
    if (taskForm) {
        taskForm.addEventListener('submit', validateTask);
    }
}

/**
 * Validate registration form
 */
function validateRegistration(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const password = formData.get('password');
    const confirmPassword = formData.get('confirm_password');
    
    if (password !== confirmPassword) {
        showAlert('Passwords do not match!', 'error');
        return false;
    }
    
    if (password.length < 6) {
        showAlert('Password must be at least 6 characters long!', 'error');
        return false;
    }
    
    // Submit form
    submitForm(e.target, 'api/register.php');
}

/**
 * Validate login form
 */
function validateLogin(e) {
    e.preventDefault();
    submitForm(e.target, 'api/login.php');
}

/**
 * Validate task form
 */
function validateTask(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const taskDate = formData.get('task_date');
    const taskTime = formData.get('task_time');
    
    // Validate date is not in the past
    const selectedDateTime = new Date(taskDate + ' ' + taskTime);
    const now = new Date();
    
    if (selectedDateTime < now) {
        showAlert('Task date and time cannot be in the past!', 'error');
        return false;
    }
    
    submitForm(e.target, 'api/task.php');
}

/**
 * Submit form via AJAX
 */
async function submitForm(form, url) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.textContent = 'Loading...';
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert(data.message, 'success');
            
            // Reset form
            form.reset();
            
            // Redirect if specified
            if (data.redirect) {
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            }
            
            // Reload tasks if task form
            if (url.includes('task.php')) {
                loadTasks();
            }
        } else {
            showAlert(data.message, 'error');
        }
    } catch (error) {
        console.error('Form submission error:', error);
        showAlert('An error occurred. Please try again.', 'error');
    } finally {
        // Restore button state
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
}

/**
 * Show alert message
 */
function showAlert(message, type = 'info') {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create new alert
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    
    // Add to page
    const container = document.querySelector('.main-content') || document.body;
    container.insertBefore(alert, container.firstChild);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        alert.remove();
    }, 5000);
}

/**
 * Load tasks for current user
 */
async function loadTasks() {
    try {
        const response = await fetch('api/tasks.php');
        const data = await response.json();
        
        if (data.success) {
            tasks = data.tasks;
            renderTasks();
            updateStatistics();
        }
    } catch (error) {
        console.error('Load tasks error:', error);
    }
}

/**
 * Render tasks in the UI
 */
function renderTasks() {
    const container = document.querySelector('#tasksContainer');
    if (!container) return;
    
    container.innerHTML = '';
    
    if (tasks.length === 0) {
        container.innerHTML = '<p class="text-center">No tasks found.</p>';
        return;
    }
    
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
            <span><i class="icon-calendar"></i> ${formatDate(task.task_date)}</span>
            <span><i class="icon-clock"></i> ${task.task_time}</span>
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
 * Update statistics
 */
function updateStatistics() {
    const totalTasks = tasks.length;
    const completedTasks = tasks.filter(t => t.status === 'completed').length;
    const pendingTasks = totalTasks - completedTasks;
    
    updateStatCard('totalTasks', totalTasks);
    updateStatCard('completedTasks', completedTasks);
    updateStatCard('pendingTasks', pendingTasks);
}

/**
 * Update stat card
 */
function updateStatCard(id, value) {
    const element = document.querySelector(`#${id}`);
    if (element) {
        element.textContent = value;
    }
}

/**
 * Complete task
 */
async function completeTask(taskId) {
    try {
        const response = await fetch('api/task.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                task_id: taskId, 
                action: 'complete' 
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('Task marked as completed!', 'success');
            loadTasks();
        } else {
            showAlert(data.message, 'error');
        }
    } catch (error) {
        console.error('Complete task error:', error);
        showAlert('An error occurred. Please try again.', 'error');
    }
}

/**
 * Delete task
 */
async function deleteTask(taskId) {
    if (!confirm('Are you sure you want to delete this task?')) {
        return;
    }
    
    try {
        const response = await fetch('api/task.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ task_id: taskId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('Task deleted successfully!', 'success');
            loadTasks();
        } else {
            showAlert(data.message, 'error');
        }
    } catch (error) {
        console.error('Delete task error:', error);
        showAlert('An error occurred. Please try again.', 'error');
    }
}

/**
 * Format date for display
 */
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

/**
 * Initialize calendar
 */
function initializeCalendar() {
    const calendarGrid = document.querySelector('.calendar-grid');
    if (!calendarGrid) return;
    
    const today = new Date();
    const currentMonth = today.getMonth();
    const currentYear = today.getFullYear();
    
    // Get first day of month
    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    
    // Clear calendar
    calendarGrid.innerHTML = '';
    
    // Add day headers
    const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    dayHeaders.forEach(day => {
        const header = document.createElement('div');
        header.className = 'calendar-header-day';
        header.textContent = day;
        calendarGrid.appendChild(header);
    });
    
    // Add empty cells for days before month starts
    for (let i = 0; i < firstDay; i++) {
        const emptyDay = document.createElement('div');
        calendarGrid.appendChild(emptyDay);
    }
    
    // Add days of month
    for (let day = 1; day <= daysInMonth; day++) {
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day';
        dayElement.textContent = day;
        
        // Mark today
        if (day === today.getDate()) {
            dayElement.classList.add('today');
        }
        
        // Check for tasks on this day
        const dayTasks = tasks.filter(task => {
            const taskDate = new Date(task.task_date);
            return taskDate.getDate() === day && 
                   taskDate.getMonth() === currentMonth && 
                   taskDate.getFullYear() === currentYear;
        });
        
        if (dayTasks.length > 0) {
            dayElement.classList.add('has-tasks');
            dayElement.title = `${dayTasks.length} task(s)`;
        }
        
        calendarGrid.appendChild(dayElement);
    }
}

// Export functions for global access
window.app = {
    showAlert,
    loadTasks,
    completeTask,
    deleteTask,
    initializeCalendar
};
