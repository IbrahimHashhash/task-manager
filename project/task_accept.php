<?php
require_once 'db.php.inc.php'; 

if ($_SESSION['role'] !== 'Team Member') {
    echo "Access Denied: Only Team Members can view assignments.";
    exit;
}

if (isset($_POST['accept_task']) && isset($_POST['task_id'])) {
    $task_id = $_POST['task_id'];

    try {
        $sql_update = "UPDATE tasks SET status = 'In Progress' WHERE task_id = :task_id";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute(['task_id' => $task_id]);

        $sql_assignment_update = "UPDATE task_assignments SET is_accepted = 1 WHERE task_id = :task_id AND user_id = :user_id";
        $stmt_assignment_update = $pdo->prepare($sql_assignment_update);
        $stmt_assignment_update->execute([
            'task_id' => $task_id, 
            'user_id' => $_SESSION['user_id']
        ]);

        $success_message = "Task successfully accepted and activated.";
        header("Location: navigation.php?action=accept_task");
        exit;
    } catch (Exception $e) {
        echo "Error accepting task: " . $e->getMessage();
    }
}

if (isset($_POST['reject_task']) && isset($_POST['task_id'])) {
    $task_id = $_POST['task_id'];

    try {
        $sql_delete = "DELETE FROM task_assignments WHERE task_id = :task_id AND user_id = :user_id";
        $stmt_delete = $pdo->prepare($sql_delete);
        $stmt_delete->execute([
            'task_id' => $task_id, 
            'user_id' => $_SESSION['user_id']
        ]);

        $success_message = "Task assignment successfully rejected.";
        header("Location: navigation.php?action=reject_task");
        exit;
    } catch (Exception $e) {
        echo "Error rejecting task: " . $e->getMessage();
    }
}

$sql_assignments = "SELECT t.task_id, t.task_name, p.title AS project_name, t.start_date 
                    FROM tasks t
                    JOIN projects p ON t.project_id = p.project_id
                    JOIN task_assignments ta ON t.task_id = ta.task_id
                    WHERE ta.user_id = :user_id AND ta.is_accepted = 0";
$stmt_assignments = $pdo->prepare($sql_assignments);
$stmt_assignments->execute(['user_id' => $_SESSION['user_id']]);
$assignments = $stmt_assignments->fetchAll();

$is_viewing_details = isset($_POST['view_task']);
?>

<h2>Assigned Tasks</h2>

<?php if (isset($success_message)): ?>
    <p class="success"><?php echo $success_message; ?></p>
<?php endif; ?>

<?php if (!$is_viewing_details): ?>
    <?php if (empty($assignments)): ?>
        <p>No tasks available to confirm.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Task ID</th>
                    <th>Task Name</th>
                    <th>Project Name</th>
                    <th>Start Date</th>
                    <th>View Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assignments as $assignment): ?>
                    <tr>
                        <td><?php echo $assignment['task_id']; ?></td>
                        <td><?php echo $assignment['task_name']; ?></td>
                        <td><?php echo $assignment['project_name']; ?></td>
                        <td><?php echo $assignment['start_date']; ?></td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="task_id" value="<?php echo $assignment['task_id']; ?>">
                                <button type="submit" name="view_task">View</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php endif; ?>

<?php
if ($is_viewing_details && isset($_POST['task_id'])) {
    $task_id = $_POST['task_id'];

    $sql_task_details = "SELECT t.task_id, t.task_name, p.title AS project_name, t.start_date, t.due_date, ta.role
                         FROM tasks t
                         JOIN projects p ON t.project_id = p.project_id
                         JOIN task_assignments ta ON t.task_id = ta.task_id
                         WHERE t.task_id = :task_id AND ta.user_id = :user_id";
    $stmt_task_details = $pdo->prepare($sql_task_details);
    $stmt_task_details->execute([
        'task_id' => $task_id, 
        'user_id' => $_SESSION['user_id']
    ]);
    $task_details = $stmt_task_details->fetch();

    if (!$task_details) {
        echo "Task not found or you do not have permission to view it.";
        exit;
    }
?>

<form method="post" action="">
    <input type="hidden" name="task_id" value="<?php echo $task_details['task_id']; ?>">
    <fieldset>
        <p><strong>Task Name:</strong> <?php echo $task_details['task_name']; ?></p>
        <p><strong>Project Name:</strong> <?php echo $task_details['project_name']; ?></p>
        <p><strong>Start Date:</strong> <?php echo $task_details['start_date']; ?></p>
        <p><strong>Due Date:</strong> <?php echo $task_details['due_date']; ?></p>
        <p><strong>Role:</strong> <?php echo $task_details['role']; ?></p>

        <button type="submit" name="accept_task">Accept Task</button>
        <button type="submit" name="reject_task">Reject Task</button>
    </fieldset>
</form>

<?php
}
?>
