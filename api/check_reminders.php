<?php
/**
 * Check Reminders API
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

$user_id = $_SESSION['user_id'];
$current_datetime = getCurrentDateTime();

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Find tasks that need reminders (within 1 minute of current time and not already reminded)
    $query = "SELECT * FROM tasks 
              WHERE user_id = :user_id 
              AND status = 'pending' 
              AND reminder_sent = FALSE 
              AND CONCAT(task_date, ' ', task_time) <= :current_datetime
              AND CONCAT(task_date, ' ', task_time) >= DATE_SUB(:current_datetime, INTERVAL 1 MINUTE)
              ORDER BY task_date, task_time";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':current_datetime', $current_datetime);
    $stmt->execute();
    
    $reminders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Mark these tasks as reminder sent
    if (!empty($reminders)) {
        $task_ids = array_column($reminders, 'id');
        $placeholders = str_repeat('?,', count($task_ids) - 1) . '?';
        
        $update_query = "UPDATE tasks SET reminder_sent = TRUE WHERE id IN ($placeholders)";
        $update_stmt = $db->prepare($update_query);
        $update_stmt->execute($task_ids);
    }
    
    // Format reminders for response
    $formatted_reminders = [];
    foreach ($reminders as $reminder) {
        $formatted_reminders[] = [
            'id' => $reminder['id'],
            'title' => $reminder['title'],
            'description' => $reminder['description'] ?? 'Task reminder',
            'time' => date('h:i A', strtotime($reminder['task_time'])),
            'priority' => $reminder['priority'],
            'type' => $reminder['task_type']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'reminders' => $formatted_reminders,
        'count' => count($formatted_reminders),
        'timestamp' => $current_datetime
    ]);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $exception->getMessage()]);
}
?>
