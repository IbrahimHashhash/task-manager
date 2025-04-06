<?php
require_once 'db.php.inc.php'; 

if ($_SESSION['role'] !== 'Project Leader') {
    echo "Access Denied: Only Project Leaders can assign team members.";
    exit;
}

$project_id = $_GET['project_id'] ?? null;
$task_id = $_GET['task_id'] ?? null;
$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $task_id = $_POST['task_id'];
    $team_member_id = $_POST['team_member'];
    $role = $_POST['role'];
    $contribution = intval($_POST['contribution']);

    $sql_contribution = "SELECT SUM(contribution) AS total FROM task_assignments WHERE task_id = :task_id";
    $stmt_contribution = $pdo->prepare($sql_contribution);
    $stmt_contribution->execute(['task_id' => $task_id]);
    $total_contribution = intval($stmt_contribution->fetchColumn());

    if (($total_contribution + $contribution) > 100) {
        $error_message = "Total contribution cannot exceed 100%.";
    } elseif (empty($team_member_id)) {
        $error_message = "Please select a team member.";
    } else {
        $sql_assign = "INSERT INTO task_assignments (task_id, user_id, role, contribution) 
                       VALUES (:task_id, :user_id, :role, :contribution)";
        $stmt_assign = $pdo->prepare($sql_assign);
        $stmt_assign->execute([
            'task_id' => $task_id,
            'user_id' => $team_member_id,
            'role' => $role,
            'contribution' => $contribution
        ]);
        $success_message = "Team member successfully assigned.";
    }
}

if (!$project_id) {
    $sql_projects = "SELECT project_id, title FROM projects WHERE project_leader_id = :user_id";
    $stmt_projects = $pdo->prepare($sql_projects);
    $stmt_projects->execute(['user_id' => $_SESSION['user_id']]);
    $projects = $stmt_projects->fetchAll();
    ?>

    <form method="GET" action="navigation.php" id="choose-project">
        <input type="hidden" name="action" value="assign_team_members">
        <fieldset>

        <label for="project_id">Select a Project:</label>
        <select name="project_id" id="project_id" required>
            <option value="">Choose a Project</option>
            <?php foreach ($projects as $project): ?>
                <option value="<?php echo $project['project_id']; ?>">
                    <?php echo $project['title']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">View Tasks</button>
        </fieldset>
    </form>

    <?php
} elseif (!$task_id) {
    $sql_tasks = "SELECT * FROM tasks WHERE project_id = :project_id";
    $stmt_tasks = $pdo->prepare($sql_tasks);
    $stmt_tasks->execute(['project_id' => $project_id]);
    $tasks = $stmt_tasks->fetchAll();
    ?>

    <h2>Tasks for Project: <?php echo $project_id; ?></h2>
    <?php if (empty($tasks)): ?>
        <p>No tasks found for this project.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Task ID</th>
                    <th>Task Name</th>
                    <th>Start Date</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                    <tr>
                        <td><?php echo $task['task_id']; ?></td>
                        <td><?php echo $task['task_name']; ?></td>
                        <td><?php echo $task['start_date']; ?></td>
                        <td><?php echo $task['due_date']; ?></td>
                        <td><?php echo $task['status']; ?></td>
                        <td><?php echo $task['priority']; ?></td>
                        <td>
                            <a href="navigation.php?action=assign_team_members&project_id=<?php echo $project_id; ?>&task_id=<?php echo $task['task_id']; ?>">
                                Assign Team Members
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <?php
} else {
    $sql_team_members = "SELECT user_id, full_name FROM users WHERE role = 'Team Member'";
    $stmt_team_members = $pdo->prepare($sql_team_members);
    $stmt_team_members->execute();
    $team_members = $stmt_team_members->fetchAll();

    ?>

    <h2>Assign Team Members to Task</h2>
    <?php if ($error_message): ?>
        <p class="error"><?php echo $error_message; ?></p>
    <?php elseif ($success_message): ?>
        <p class="success"><?php echo $success_message; ?></p>

    <?php endif; ?>
    <form method="POST" action="navigation.php?action=assign_team_members&project_id=<?php echo $project_id; ?>&task_id=<?php echo $task_id; ?>" id="assign-team-members">
        <fieldset>
        <input type="hidden" name="task_id" value="<?php echo $task_id; ?>">
        <label for="team_member">Team Member:</label>
        <select name="team_member" id="team_member" required>
            <option value="">Select Team Member</option>
            <?php foreach ($team_members as $member): ?>
                <option value="<?php echo $member['user_id']; ?>">
                    <?php echo $member['full_name']; ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label for="role">Role:</label>
        <select name="role" id="role" required>
            <option value="Developer">Developer</option>
            <option value="Designer">Designer</option>
            <option value="Tester">Tester</option>
            <option value="Analyst">Analyst</option>
            <option value="Support">Support</option>
        </select><br>

        <label for="contribution">Contribution Percentage:</label>
        <input type="number" name="contribution" id="contribution" min="1" max="100" required><br>

        <button type="submit">Assign</button>
        </fieldset>
    </form>

    <?php
}
?>
