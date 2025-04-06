<?php
include "header.php";
require_once 'db.php.inc.php'; 


$task_id = $_GET['task_id'] ?? null;

if (!$task_id) {
    echo "No task selected.";
    exit;
}

$sql_task = "SELECT t.task_id, t.task_name, t.description, p.title AS project_name, 
                    t.start_date, t.due_date, t.progress, t.status, t.priority
             FROM tasks t
             JOIN projects p ON t.project_id = p.project_id
             WHERE t.task_id = :task_id";
$stmt_task = $pdo->prepare($sql_task);
$stmt_task->execute(['task_id' => $task_id]);
$task = $stmt_task->fetch();

if (!$task) {
    echo "Task not found.";
    exit;
}

$sql_team_members = "SELECT u.user_id, u.full_name, u.email, ta.role, ta.contribution, ta.is_accepted
                     FROM task_assignments ta
                     JOIN users u ON ta.user_id = u.user_id
                     WHERE ta.task_id = :task_id";
$stmt_team_members = $pdo->prepare($sql_team_members);
$stmt_team_members->execute(['task_id' => $task_id]);
$team_members = $stmt_team_members->fetchAll();
?>

<section class="task-details">
    <div class="t-info">
        <h2>Task Information</h2>
        <p><strong>Task ID:</strong> <?php echo $task['task_id']; ?></p>
        <p><strong>Task Name:</strong> <?php echo $task['task_name']; ?></p>
        <p><strong>Description:</strong> <?php echo $task['description']; ?></p>
        <p><strong>Project:</strong> <?php echo $task['project_name']; ?></p>
        <p><strong>Start Date:</strong> <?php echo $task['start_date']; ?></p>
        <p><strong>End Date:</strong> <?php echo $task['due_date']; ?></p>
        <p><strong>Completion Percentage:</strong> <?php echo $task['progress'] . "%"; ?></p>
        <p><strong>Status:</strong> <?php echo $task['status']; ?></p>
        <p><strong>Priority:</strong> <?php echo $task['priority']; ?></p>
        </div>

    <div>
        <h2>Team Members</h2>
        <?php if (empty($team_members)): ?>
            <p>No team members assigned to this task.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th> Photo</th>
                        <th>Member ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Effort (%)</th>
                        <th>Status</th>
                        <th>Confirmation</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($team_members as $member): ?>
                        <tr>
                            <td> <img src="images/pfp.jpg" id="icon"></td>
                           <td><?php echo $member['user_id']; ?></td>
                            <td><?php echo $member['user_id']; ?></td>
                            <td><?php echo $member['full_name']; ?></td>
                            <td><?php echo $member['email']; ?></td>
                            <td><?php echo $member['role']; ?></td>
                            <td><?php echo $member['contribution']; ?>%</td>
                            <td><?php echo $member['is_accepted'] ? "Accepted" : "Pending"; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</section>
<?php include "footer.html";?>