<?php
/**
 * Tasks API - Simplified endpoint for fetching tasks
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

// Handle GET requests only
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$user_id = $_SESSION['user_id'];
$filter = $_GET['filter'] ?? 'all';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Build query based on filter
    $query = "SELECT * FROM tasks WHERE user_id = :user_id";
    $params = [':user_id' => $user_id];
    
    if ($filter === 'today') {
        $query .= " AND task_date = CURDATE()";
    } elseif ($filter === 'upcoming') {
        $query .= " AND task_date >= CURDATE() AND status = 'pending'";
    } elseif ($filter === 'completed') {
        $query .= " AND status = 'completed'";
    } elseif ($filter === 'pending') {
        $query .= " AND status = 'pending'";
    }
    
    $query .= " ORDER BY task_date ASC, task_time ASC";
    
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->execute();
    
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format dates for better display
    foreach ($tasks as &$task) {
        $task['formatted_date'] = date('M d, Y', strtotime($task['task_date']));
        $task['formatted_time'] = date('h:i A', strtotime($task['task_time']));
        $task['datetime'] = $task['task_date'] . ' ' . $task['task_time'];
    }
    
    echo json_encode([
        'success' => true,
        'tasks' => $tasks,
        'count' => count($tasks)
    ]);
    
} catch(PDOException $exception) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $exception->getMessage()]);
}
?>
