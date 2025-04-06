<?php
require_once "db.php.inc.php";

if ($_SESSION['role'] !== 'Project Leader') {
    echo "Access denied. Only Project Leaders can create tasks.";
    exit();
}

$user_id = $_SESSION['user_id'];
$sql_projects = "SELECT project_id, title, start_date, end_date 
                 FROM projects 
                 WHERE project_leader_id = :user_id";
$stmt_projects = $pdo->prepare($sql_projects);
$stmt_projects->bindValue(':user_id', $user_id);
$stmt_projects->execute();
$projects = $stmt_projects->fetchAll();

if (empty($projects)) {
    echo "No active projects found for this user.";
    exit();
}

$success_message = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required_fields = ['task_name', 'task_description', 'project_id', 'start_date', 'due_date', 'effort', 'priority', 'status'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $error_message = "Error: Missing required field '$field'.";
            break;
        }
    }

    if (empty($error_message)) {
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
        $stmt->bindValue(':project_id', $project_id);
        $stmt->execute();
        $project_dates = $stmt->fetch();

        if (!$project_dates) {
            $error_message = "Error: Invalid project ID.";
        } elseif ($start_date < $project_dates['start_date'] || $due_date > $project_dates['end_date']) {
            $error_message = "Error: Task dates must be within the project's start and end dates.";
        } else {
            try {
                $sql = "INSERT INTO tasks (task_name, description, project_id, priority, status, start_date, due_date) 
                        VALUES (:task_name, :task_description, :project_id, :priority, :status, :start_date, :due_date)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':task_name' => $task_name,
                    ':task_description' => $task_description,
                    ':project_id' => $project_id,
                    ':priority' => $priority,
                    ':status' => $status,
                    ':start_date' => $start_date,
                    ':due_date' => $due_date
                ]);

                $success_message = "Task '$task_name' successfully created.";
            } catch (PDOException $e) {
                $error_message = "Error: " . $e->getMessage();
            }
        }
    }
}
?>
<h2>Task Creation</h2>
<form action="" method="post" id="create-task-form">
    <fieldset>
        <legend>Task Info</legend>
    <label for="task-name">Task Name:</label>
    <input type="text" id="task-name" name="task_name" required><br>

    <label for="task-description">Description:</label>
    <textarea id="task-description" name="task_description" required></textarea><br>

    <label for="project-id">Project:</label>
    <select id="project-id" name="project_id" required>
        <option value="">Select a project</option>
        <?php foreach ($projects as $project): ?>
            <option value="<?= $project['project_id'] ?>" 
                    data-start-date="<?= $project['start_date'] ?>" 
                    data-end-date="<?= $project['end_date'] ?>">
                <?= $project['title'] ?>
            </option>
        <?php endforeach; ?>
    </select><br>
    <label for="start-date">Start Date:</label>
    <input type="date" id="start-date" name="start_date" required><br>

    <label for="due-date">Due Date:</label>
    <input type="date" id="due-date" name="due_date" required><br>

    <label for="effort">Effort (man-months):</label>
    <input type="number" id="effort" name="effort" min="1" step="0.1" required><br>

    <label for="priority">Priority:</label>
    <select id="priority" name="priority" required>
        <option value="Low">Low</option>
        <option value="Medium">Medium</option>
        <option value="High">High</option>
    </select><br>

    <label for="status">Status:</label>
    <select id="status" name="status" required>
        <option value="Pending" selected>Pending</option>
        <option value="In Progress">In Progress</option>
        <option value="Completed">Completed</option>
    </select><br>

    <button type="submit">Create Task</button>
    </fieldset>
</form>

<?php if (!empty($success_message)): ?>
    <p class="success">
        <?= $success_message ?>
    </p>
<?php endif; ?>

<?php if (!empty($error_message)): ?>
    <p class="error">
        <?= $error_message ?>
    </p>
<?php endif; ?>
