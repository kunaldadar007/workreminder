<?php
/**
 * Chatbot API
 * Work Reminder and Chat Bot System
 * B.Sc Computer Science Final Year Project
 */

require_once '../config/config.php';

// Set response headers
header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Handle POST requests only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$query = strtolower(trim($input['query'] ?? ''));

if (empty($query)) {
    echo json_encode(['success' => false, 'message' => 'Query is required']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Log the user query
    logChatbotQuery($user_id, $query);
    
    // Process the query and generate response
    $response = processChatbotQuery($user_id, $query);
    
    // Log the bot response
    logChatbotResponse($user_id, $response);
    
    echo json_encode([
        'success' => true,
        'response' => $response
    ]);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $exception->getMessage()]);
}

/**
 * Process chatbot query using simple NLP rules
 */
function processChatbotQuery($user_id, $query) {
    $database = new Database();
    $db = $database->getConnection();
    
    // Greeting patterns
    if (preg_match('/^(hi|hello|hey|good morning|good afternoon|good evening)/', $query)) {
        $greetings = ['Hello! How can I help you with your tasks today?', 
                     'Hi there! What can I do for you?', 
                     'Hey! Ready to manage your tasks?'];
        return $greetings[array_rand($greetings)];
    }
    
    // Today's tasks patterns
    if (preg_match('/(what|show|list|tell me about).*today/', $query) || 
        preg_match('/today.*task/', $query)) {
        return getTodayTasks($db, $user_id);
    }
    
    // Tomorrow's tasks patterns
    if (preg_match('/(what|show|list|tell me about).*tomorrow/', $query) || 
        preg_match('/tomorrow.*task/', $query)) {
        return getTomorrowTasks($db, $user_id);
    }
    
    // Monthly tasks patterns
    if (preg_match('/(monthly|month|this month).*task/', $query) || 
        preg_match('/task.*month/', $query)) {
        return getMonthlyTasks($db, $user_id);
    }
    
    // Upcoming tasks patterns
    if (preg_match('/(upcoming|next|future).*task/', $query)) {
        return getUpcomingTasks($db, $user_id);
    }
    
    // Completed tasks patterns
    if (preg_match('/(completed|done|finished).*task/', $query)) {
        return getCompletedTasks($db, $user_id);
    }
    
    // Pending tasks patterns
    if (preg_match('/(pending|remaining|left).*task/', $query)) {
        return getPendingTasks($db, $user_id);
    }
    
    // High priority tasks
    if (preg_match('/(high priority|important|urgent).*task/', $query)) {
        return getHighPriorityTasks($db, $user_id);
    }
    
    // Task count patterns
    if (preg_match('/(how many|count|number).*task/', $query)) {
        return getTaskCount($db, $user_id);
    }
    
    // Help patterns
    if (preg_match('/(help|what can you do|commands)/', $query)) {
        return getHelpMessage();
    }
    
    // Reminder patterns
    if (preg_match('/remind me about/', $query)) {
        return handleReminderRequest($query);
    }
    
    // Default response
    $default_responses = [
        "I'm not sure how to help with that. Try asking about today's tasks, monthly schedule, or type 'help' for more options.",
        "I can help you with task management! Ask me about your tasks, schedule, or type 'help' to see what I can do.",
        "Hmm, I didn't understand that. You can ask me things like 'What tasks do I have today?' or 'Show my monthly schedule'."
    ];
    
    return $default_responses[array_rand($default_responses)];
}

/**
 * Get today's tasks
 */
function getTodayTasks($db, $user_id) {
    $query = "SELECT title, task_time, priority FROM tasks 
              WHERE user_id = :user_id AND task_date = CURDATE() AND status = 'pending'
              ORDER BY task_time ASC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($tasks)) {
        return "You don't have any tasks scheduled for today. Great job staying on top of things!";
    }
    
    $response = "You have " . count($tasks) . " task(s) today:\n";
    foreach ($tasks as $task) {
        $time = date('h:i A', strtotime($task['task_time']));
        $priority = strtoupper($task['priority']);
        $response .= "• {$task['title']} at {$time} [{$priority}]\n";
    }
    
    return $response;
}

/**
 * Get tomorrow's tasks
 */
function getTomorrowTasks($db, $user_id) {
    $query = "SELECT title, task_time, priority FROM tasks 
              WHERE user_id = :user_id AND task_date = DATE_ADD(CURDATE(), INTERVAL 1 DAY) AND status = 'pending'
              ORDER BY task_time ASC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($tasks)) {
        return "You don't have any tasks scheduled for tomorrow.";
    }
    
    $response = "You have " . count($tasks) . " task(s) for tomorrow:\n";
    foreach ($tasks as $task) {
        $time = date('h:i A', strtotime($task['task_time']));
        $priority = strtoupper($task['priority']);
        $response .= "• {$task['title']} at {$time} [{$priority}]\n";
    }
    
    return $response;
}

/**
 * Get monthly tasks
 */
function getMonthlyTasks($db, $user_id) {
    $query = "SELECT title, task_date, task_time, task_type FROM tasks 
              WHERE user_id = :user_id AND MONTH(task_date) = MONTH(CURDATE()) AND YEAR(task_date) = YEAR(CURDATE())
              ORDER BY task_date ASC, task_time ASC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($tasks)) {
        return "You don't have any tasks scheduled for this month.";
    }
    
    $pending = count(array_filter($tasks, function($task) { 
        $task_datetime = $task['task_date'] . ' ' . $task['task_time'];
        return $task_datetime >= getCurrentDateTime() && $task['status'] !== 'completed';
    }));
    
    $completed = count(array_filter($tasks, function($task) { 
        return $task['status'] === 'completed';
    }));
    
    $response = "This month you have " . count($tasks) . " total tasks ({$pending} pending, {$completed} completed).\n\n";
    
    // Show upcoming tasks for this month
    $upcoming = array_filter($tasks, function($task) {
        $task_datetime = $task['task_date'] . ' ' . $task['task_time'];
        return $task_datetime >= getCurrentDateTime() && $task['status'] !== 'completed';
    });
    
    if (!empty($upcoming)) {
        $response .= "Upcoming tasks:\n";
        foreach (array_slice($upcoming, 0, 5) as $task) {
            $date = date('M d', strtotime($task['task_date']));
            $time = date('h:i A', strtotime($task['task_time']));
            $response .= "• {$task['title']} on {$date} at {$time}\n";
        }
        
        if (count($upcoming) > 5) {
            $response .= "• ... and " . (count($upcoming) - 5) . " more tasks\n";
        }
    }
    
    return $response;
}

/**
 * Get upcoming tasks
 */
function getUpcomingTasks($db, $user_id) {
    $query = "SELECT title, task_date, task_time FROM tasks 
              WHERE user_id = :user_id AND task_date >= CURDATE() AND status = 'pending'
              ORDER BY task_date ASC, task_time ASC
              LIMIT 10";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($tasks)) {
        return "You don't have any upcoming tasks.";
    }
    
    $response = "Here are your upcoming tasks:\n";
    foreach ($tasks as $task) {
        $date = date('M d', strtotime($task['task_date']));
        $time = date('h:i A', strtotime($task['task_time']));
        $response .= "• {$task['title']} on {$date} at {$time}\n";
    }
    
    return $response;
}

/**
 * Get completed tasks
 */
function getCompletedTasks($db, $user_id) {
    $query = "SELECT COUNT(*) as completed FROM tasks 
              WHERE user_id = :user_id AND status = 'completed'";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $completed = $result['completed'];
    
    if ($completed == 0) {
        return "You haven't completed any tasks yet. Keep going!";
    }
    
    // Get recent completed tasks
    $recent_query = "SELECT title, updated_at FROM tasks 
                     WHERE user_id = :user_id AND status = 'completed'
                     ORDER BY updated_at DESC
                     LIMIT 5";
    
    $recent_stmt = $db->prepare($recent_query);
    $recent_stmt->bindParam(':user_id', $user_id);
    $recent_stmt->execute();
    
    $recent_tasks = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response = "Great job! You've completed {$completed} task(s) so far.\n\n";
    
    if (!empty($recent_tasks)) {
        $response .= "Recently completed:\n";
        foreach ($recent_tasks as $task) {
            $date = date('M d', strtotime($task['updated_at']));
            $response .= "• {$task['title']} (completed on {$date})\n";
        }
    }
    
    return $response;
}

/**
 * Get pending tasks
 */
function getPendingTasks($db, $user_id) {
    $query = "SELECT COUNT(*) as pending FROM tasks 
              WHERE user_id = :user_id AND status = 'pending'";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $pending = $result['pending'];
    
    if ($pending == 0) {
        return "You don't have any pending tasks. All caught up!";
    }
    
    $response = "You have {$pending} pending task(s). ";
    
    if ($pending > 5) {
        $response .= "That's quite a few! Try to focus on the high priority ones first.";
    } elseif ($pending > 2) {
        $response .= "You're making good progress!";
    } else {
        $response .= "Almost there!";
    }
    
    return $response;
}

/**
 * Get high priority tasks
 */
function getHighPriorityTasks($db, $user_id) {
    $query = "SELECT title, task_date, task_time FROM tasks 
              WHERE user_id = :user_id AND priority = 'high' AND status = 'pending'
              ORDER BY task_date ASC, task_time ASC";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($tasks)) {
        return "You don't have any high priority tasks. Nice work!";
    }
    
    $response = "⚠️ You have " . count($tasks) . " high priority task(s):\n";
    foreach ($tasks as $task) {
        $date = date('M d', strtotime($task['task_date']));
        $time = date('h:i A', strtotime($task['task_time']));
        $response .= "• {$task['title']} on {$date} at {$time}\n";
    }
    
    return $response;
}

/**
 * Get task count
 */
function getTaskCount($db, $user_id) {
    $query = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending
              FROM tasks WHERE user_id = :user_id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $total = $result['total'];
    $completed = $result['completed'];
    $pending = $result['pending'];
    
    if ($total == 0) {
        return "You don't have any tasks yet. Start by adding your first task!";
    }
    
    $completion_rate = round(($completed / $total) * 100, 1);
    
    return "Task Summary:\n• Total: {$total}\n• Completed: {$completed}\n• Pending: {$pending}\n• Completion Rate: {$completion_rate}%";
}

/**
 * Get help message
 */
function getHelpMessage() {
    return "I can help you with your tasks! Here's what you can ask me:\n\n" .
           "• 'What tasks do I have today?'\n" .
           "• 'Show me tomorrow's tasks'\n" .
           "• 'What's my monthly schedule?'\n" .
           "• 'Show me upcoming tasks'\n" .
           "• 'How many tasks do I have?'\n" .
           "• 'What are my high priority tasks?'\n" .
           "• 'Show me completed tasks'\n" .
           "• 'What pending tasks do I have?'\n\n" .
           "Try asking in your own words - I'm pretty smart!";
}

/**
 * Handle reminder request
 */
function handleReminderRequest($query) {
    // Simple reminder acknowledgment
    return "I'll make sure you get notified about your tasks when they're due! " .
           "The system checks for reminders every 30 seconds and will send you browser notifications.";
}

/**
 * Log chatbot query
 */
function logChatbotQuery($user_id, $query) {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $insert_query = "INSERT INTO chatbot_logs (user_id, user_query) VALUES (:user_id, :user_query)";
        $stmt = $db->prepare($insert_query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':user_query', $query);
        $stmt->execute();
        
        return $db->lastInsertId();
    } catch(PDOException $exception) {
        // Log error but don't fail the response
        error_log("Chatbot log error: " . $exception->getMessage());
        return null;
    }
}

/**
 * Log chatbot response
 */
function logChatbotResponse($user_id, $response) {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Update the last log entry with the response
        $update_query = "UPDATE chatbot_logs SET bot_response = :bot_response 
                        WHERE user_id = :user_id AND bot_response IS NULL 
                        ORDER BY id DESC LIMIT 1";
        $stmt = $db->prepare($update_query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':bot_response', $response);
        $stmt->execute();
    } catch(PDOException $exception) {
        // Log error but don't fail the response
        error_log("Chatbot response log error: " . $exception->getMessage());
    }
}
?>
