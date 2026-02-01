<?php
/**
 * Task Management API
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

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGetTasks();
        break;
    case 'POST':
        handleCreateTask();
        break;
    case 'PUT':
        handleUpdateTask();
        break;
    case 'DELETE':
        handleDeleteTask();
        break;
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}

/**
 * Handle GET requests - fetch tasks
 */
function handleGetTasks() {
    $user_id = $_SESSION['user_id'];
    $filter = $_GET['filter'] ?? 'all';
    $date = $_GET['date'] ?? null;
    
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
        } elseif ($date) {
            $query .= " AND task_date = :date";
            $params[':date'] = $date;
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
}

/**
 * Handle POST requests - create new task
 */
function handleCreateTask() {
    $user_id = $_SESSION['user_id'];
    
    $title = sanitizeInput($_POST['title'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    $task_date = $_POST['task_date'] ?? '';
    $task_time = $_POST['task_time'] ?? '';
    $task_type = $_POST['task_type'] ?? 'daily';
    $priority = $_POST['priority'] ?? 'medium';
    
    $errors = [];
    
    if (empty($title)) {
        $errors[] = "Task title is required";
    }
    
    if (empty($task_date)) {
        $errors[] = "Task date is required";
    }
    
    if (empty($task_time)) {
        $errors[] = "Task time is required";
    }
    
    if (!in_array($task_type, ['daily', 'monthly'])) {
        $errors[] = "Invalid task type";
    }
    
    if (!in_array($priority, ['low', 'medium', 'high'])) {
        $errors[] = "Invalid priority level";
    }
    
    // Validate date is not in the past
    $task_datetime = new DateTime($task_date . ' ' . $task_time);
    $now = new DateTime();
    if ($task_datetime < $now) {
        $errors[] = "Task date and time cannot be in the past";
    }
    
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
        return;
    }
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "INSERT INTO tasks (user_id, title, description, task_date, task_time, task_type, priority) 
                  VALUES (:user_id, :title, :description, :task_date, :task_time, :task_type, :priority)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':task_date', $task_date);
        $stmt->bindParam(':task_time', $task_time);
        $stmt->bindParam(':task_type', $task_type);
        $stmt->bindParam(':priority', $priority);
        
        if ($stmt->execute()) {
            $task_id = $db->lastInsertId();
            echo json_encode([
                'success' => true,
                'message' => 'Task created successfully',
                'task_id' => $task_id
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create task']);
        }
        
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $exception->getMessage()]);
    }
}

/**
 * Handle PUT requests - update task
 */
function handleUpdateTask() {
    $user_id = $_SESSION['user_id'];
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    $task_id = $input['task_id'] ?? 0;
    $action = $input['action'] ?? 'update';
    
    if (empty($task_id)) {
        echo json_encode(['success' => false, 'message' => 'Task ID is required']);
        return;
    }
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Verify task belongs to current user
        $verify_query = "SELECT id FROM tasks WHERE id = :task_id AND user_id = :user_id";
        $verify_stmt = $db->prepare($verify_query);
        $verify_stmt->bindParam(':task_id', $task_id);
        $verify_stmt->bindParam(':user_id', $user_id);
        $verify_stmt->execute();
        
        if ($verify_stmt->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'Task not found or access denied']);
            return;
        }
        
        if ($action === 'complete') {
            // Mark task as completed
            $query = "UPDATE tasks SET status = 'completed', updated_at = NOW() WHERE id = :task_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':task_id', $task_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Task marked as completed']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update task']);
            }
            
        } elseif ($action === 'update') {
            // Update task details
            $title = sanitizeInput($input['title'] ?? '');
            $description = sanitizeInput($input['description'] ?? '');
            $task_date = $input['task_date'] ?? '';
            $task_time = $input['task_time'] ?? '';
            $task_type = $input['task_type'] ?? 'daily';
            $priority = $input['priority'] ?? 'medium';
            
            $errors = [];
            
            if (empty($title)) {
                $errors[] = "Task title is required";
            }
            
            if (empty($task_date)) {
                $errors[] = "Task date is required";
            }
            
            if (empty($task_time)) {
                $errors[] = "Task time is required";
            }
            
            if (!empty($errors)) {
                echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
                return;
            }
            
            $query = "UPDATE tasks SET 
                      title = :title, 
                      description = :description, 
                      task_date = :task_date, 
                      task_time = :task_time, 
                      task_type = :task_type, 
                      priority = :priority,
                      updated_at = NOW()
                      WHERE id = :task_id";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':task_date', $task_date);
            $stmt->bindParam(':task_time', $task_time);
            $stmt->bindParam(':task_type', $task_type);
            $stmt->bindParam(':priority', $priority);
            $stmt->bindParam(':task_id', $task_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Task updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update task']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
        
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $exception->getMessage()]);
    }
}

/**
 * Handle DELETE requests - delete task
 */
function handleDeleteTask() {
    $user_id = $_SESSION['user_id'];
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    $task_id = $input['task_id'] ?? 0;
    
    if (empty($task_id)) {
        echo json_encode(['success' => false, 'message' => 'Task ID is required']);
        return;
    }
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Verify task belongs to current user
        $verify_query = "SELECT id FROM tasks WHERE id = :task_id AND user_id = :user_id";
        $verify_stmt = $db->prepare($verify_query);
        $verify_stmt->bindParam(':task_id', $task_id);
        $verify_stmt->bindParam(':user_id', $user_id);
        $verify_stmt->execute();
        
        if ($verify_stmt->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'Task not found or access denied']);
            return;
        }
        
        // Delete task
        $query = "DELETE FROM tasks WHERE id = :task_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':task_id', $task_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete task']);
        }
        
    } catch(PDOException $exception) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $exception->getMessage()]);
    }
}
?>
