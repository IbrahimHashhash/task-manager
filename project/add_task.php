<?php
require_once "db.php.inc.php";

$required_fields = ['task_name', 'task_description', 'project_id', 'start_date', 'due_date', 'effort', 'priority', 'status'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        die("Error: Missing required field '$field'.");
    }
}

$task_name = $_POST['task_name'];
$task_description = $_POST['task_description'];
$project_id = $_POST['project_id'];
$start_date = $_POST['start_date'];
$due_date = $_POST['due_date'];
$effort = $_POST['effort'];
$priority = $_POST['priority'];
$status = $_POST['status'];

$sql_project_dates = "SELECT start_date, end_date FROM projects WHERE project_id = :project_id";
$stmt = $pdo->prepare($sql_project_dates);
$stmt->execute(['project_id' => $project_id]);
$project_dates = $stmt->fetch();

if (!$project_dates) {
    die("Error: Invalid project ID.");
}

if ($start_date < $project_dates['start_date']) {
    die("Error: Task start date cannot be earlier than the project's start date.");
}
if ($due_date > $project_dates['end_date']) {
    die("Error: Task due date cannot exceed the project's end date.");
}

try {
    $sql = "INSERT INTO tasks (task_name, description, project_id, priority, status, due_date, start_date)
            VALUES (:task_name, :task_description, :project_id, :priority, :status, :due_date, :start_date)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'task_name' => $task_name,
        'task_description' => $task_description,
        'project_id' => $project_id,
        'priority' => $priority,
        'status' => $status,
        'due_date' => $due_date,
        'start_date' => $start_date
    ]);

    echo "Task '$task_name' successfully created.";
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
