<?php
require_once 'db.php.inc.php'; 

if ($_SESSION['role'] !== 'Project Leader') {
    echo "Access Denied: Only Project Leaders can assign team members.";
    exit;
}

$task_id = $_GET['task_id'] ?? null;

if (!$task_id) {
    echo "No task selected.";
    exit;
}

$sql_task = "SELECT * FROM tasks WHERE task_id = :task_id";
$stmt_task = $pdo->prepare($sql_task);
$stmt_task->execute(['task_id' => $task_id]);
$task = $stmt_task->fetch();

if (!$task) {
    echo "Task not found.";
    exit;
}

$sql_team_members = "SELECT user_id, full_name FROM users WHERE role = 'Team Member' AND user_id NOT IN (SELECT user_id FROM task_team_members WHERE task_id = :task_id)";
$stmt_team_members = $pdo->prepare($sql_team_members);
$stmt_team_members->execute(['task_id' => $task_id]);
$team_members = $stmt_team_members->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $team_member_id = $_POST['team_member'] ?? null;
    $role = $_POST['role'] ?? null;
    $contribution = $_POST['contribution'] ?? null;

    if (!is_numeric($contribution) || $contribution <= 0 || $contribution > 100) {
        $error_message = "Contribution must be a numeric value between 1 and 100.";
    } else {
        $sql_check_contribution = "SELECT SUM(contribution) AS total_contribution FROM task_team_members WHERE task_id = :task_id";
        $stmt_check_contribution = $pdo->prepare($sql_check_contribution);
        $stmt_check_contribution->execute(['task_id' => $task_id]);
        $existing_contribution = $stmt_check_contribution->fetchColumn();

        $new_total_contribution = $existing_contribution + $contribution;

        if ($new_total_contribution > 100) {
            $error_message = "Total contribution cannot exceed 100%. Current contribution: $existing_contribution%.";
        } elseif ($new_total_contribution < 100 && isset($_POST['finalize'])) {
            $error_message = "Total contribution must be exactly 100% to finalize.";
        } else {
            $sql_insert_assignment = "INSERT INTO task_team_members (task_id, user_id, role, contribution) VALUES (:task_id, :user_id, :role, :contribution)";
            $stmt_insert_assignment = $pdo->prepare($sql_insert_assignment);
            $stmt_insert_assignment->execute([
                'task_id' => $task_id,
                'user_id' => $team_member_id,
                'role' => $role,
                'contribution' => $contribution
            ]);

            $success_message = "Team member successfully assigned.";
            if (isset($_POST['finalize']) && $new_total_contribution == 100) {
                $success_message .= " Task allocation finalized.";
            }
        }
    }
}
?>

    <h2>Assign Team Member to Task</h2>

    <?php if (isset($error_message)): ?>
        <p class="error"><?php echo $error_message; ?></p>
        <?php elseif (isset($success_message)): ?>
        <p class="success"><?php echo $success_message; ?></p>
        <?php endif; ?>

    <form action="task_allocate.php?task_id=<?php echo $task['task_id']; ?>" method="post">
        <fieldset>
            <legend>Task Details</legend>
            <p><strong>Task Name:</strong> <?php echo $task['task_name']; ?></p>
        </fieldset>

        <fieldset>
            <legend>Assign Team Member</legend>
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
            <input type="number" id="contribution" name="contribution" min="1" max="100" required><br>
        </fieldset>

        <button type="submit" name="assign">Assign Team Member</button>
        <button type="submit" name="finalize">Finalize Allocation</button>
    </form>
