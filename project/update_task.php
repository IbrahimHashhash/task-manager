<?php
require_once 'db.php.inc.php';

if ($_SESSION['role'] !== 'Team Member') {
    echo "Access Denied: Only Team Members can update task progress.";
    exit;
}

$user_id = $_SESSION['user_id'];
$filter_conditions = '';
$search_value = '';

if (isset($_POST['search'])) {
    $search_value = $_POST['search_value'];
    if ($_POST['search_filter'] === 'task_id') {
        $filter_conditions = "AND t.task_id LIKE :search_value";
    } elseif ($_POST['search_filter'] === 'task_name') {
        $filter_conditions = "AND t.task_name LIKE :search_value";
    } elseif ($_POST['search_filter'] === 'project_name') {
        $filter_conditions = "AND p.title LIKE :search_value";
    }
}

$sql_search = "
    SELECT 
        ta.task_assignment_id, 
        t.task_id, 
        t.task_name, 
        p.title AS project_name, 
        t.start_date, 
        t.status AS task_status, 
        t.progress AS task_progress, 
        ta.progress AS member_progress 
    FROM tasks t
    JOIN projects p ON t.project_id = p.project_id
    JOIN task_assignments ta ON t.task_id = ta.task_id
    WHERE ta.user_id = :user_id AND ta.is_accepted = 1 $filter_conditions";
$stmt_search = $pdo->prepare($sql_search);
$stmt_search->bindValue(':user_id', $user_id);
if ($search_value) {
    $stmt_search->bindValue(':search_value', '%' . $search_value . '%');
}
$stmt_search->execute();
$tasks = $stmt_search->fetchAll();

if (isset($_POST['update_task']) && isset($_POST['task_assignment_id'])) {
    $task_assignment_id = $_POST['task_assignment_id'];
    $progress = (int) $_POST['progress'];

    $sql_update_assignment = "
        UPDATE task_assignments 
        SET progress = :progress 
        WHERE task_assignment_id = :task_assignment_id AND user_id = :user_id";
    $stmt_update_assignment = $pdo->prepare($sql_update_assignment);
    $stmt_update_assignment->execute([
        'progress' => $progress,
        'task_assignment_id' => $task_assignment_id,
        'user_id' => $user_id
    ]);

    $sql_calculate_task_progress = "
        SELECT 
            ROUND(SUM(ta.contribution / 100 * ta.progress / 100) * 100, 2) AS overall_progress
        FROM task_assignments ta
        WHERE ta.task_id = (SELECT task_id FROM task_assignments WHERE task_assignment_id = :task_assignment_id)";
    $stmt_calculate_task_progress = $pdo->prepare($sql_calculate_task_progress);
    $stmt_calculate_task_progress->execute(['task_assignment_id' => $task_assignment_id]);
    $overall_progress = $stmt_calculate_task_progress->fetchColumn();

    $new_status = 'Pending';
    if ($overall_progress == 100) {
        $new_status = 'Completed';
    } elseif ($overall_progress > 0) {
        $new_status = 'In Progress';
    }

    $sql_update_task = "
        UPDATE tasks 
        SET progress = :progress, status = :status 
        WHERE task_id = (SELECT task_id FROM task_assignments WHERE task_assignment_id = :task_assignment_id)";
    $stmt_update_task = $pdo->prepare($sql_update_task);
    $stmt_update_task->execute([
        'progress' => $overall_progress,
        'status' => $new_status,
        'task_assignment_id' => $task_assignment_id
    ]);

    $success_message = "Task progress updated successfully.";
    header("Location: navigation.php?action=update_task_progress");
    exit;
}
?>

<h2>Update Task Progress</h2>

<?php if (isset($success_message)): ?>
    <p class="success"><?php echo $success_message; ?></p>
<?php endif; ?>

<form method="post" action="" class="search">
    <label for="search_filter">Search By:</label>
    <select name="search_filter" id="search_filter">
        <option value="task_id" <?php echo $search_value === 'task_id' ? 'selected' : ''; ?>>Task ID</option>
        <option value="task_name" <?php echo $search_value === 'task_name' ? 'selected' : ''; ?>>Task Name</option>
        <option value="project_name" <?php echo $search_value === 'project_name' ? 'selected' : ''; ?>>Project Name</option>
    </select>
    <input type="text" name="search_value" value="<?php echo $search_value; ?>" required>
    <button type="submit" name="search">Search</button>
</form>

<?php if (!empty($tasks)): ?>
    <table>
        <thead>
            <tr>
                <th>Task ID</th>
                <th>Task Name</th>
                <th>Project Name</th>
                <th>Start Date</th>
                <th>Your Progress</th>
                <th>Overall Status</th>
                <th>Update</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tasks as $task): ?>
                <tr>
                    <td><?php echo $task['task_id']; ?></td>
                    <td><?php echo $task['task_name']; ?></td>
                    <td><?php echo $task['project_name']; ?></td>
                    <td><?php echo $task['start_date']; ?></td>
                    <td>
                        <form method="post" action="">
                            <input type="hidden" name="task_assignment_id" value="<?php echo $task['task_assignment_id']; ?>">
                            <input type="range" name="progress" min="0" max="100" value="<?php echo $task['member_progress']; ?>" required 
                                >
                            <output><?php echo $task['member_progress']; ?>%</output>
                    </td>
                    <td><?php echo $task['task_status']; ?></td>
                    <td>
                        <button type="submit" name="update_task">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No tasks found.</p>
<?php endif; ?>
