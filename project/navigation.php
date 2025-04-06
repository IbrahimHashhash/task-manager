<?php
include "header.php";
require_once "db.php.inc.php";

if (!isset($_SESSION['user_id'])) {
    echo "Please log in to access this page.";
    exit();
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

try {
    $stmt = $pdo->prepare("SELECT role FROM users WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch();

    if ($user) {
        $role = $user['role'];
    } else {
        echo "User not found.";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

$hasUnassignedTasks = false;
if ($role === 'Project Leader') {
    $sql_unassigned = "
        SELECT COUNT(*) AS unassigned_count 
        FROM projects p
        JOIN tasks t ON p.project_id = t.project_id
        WHERE p.project_leader_id = :user_id
        AND t.task_id NOT IN (
            SELECT DISTINCT task_id FROM task_team_members
        )
    ";
    $stmt_unassigned = $pdo->prepare($sql_unassigned);
    $stmt_unassigned->execute(['user_id' => $user_id]);
    $result = $stmt_unassigned->fetch();

    $hasUnassignedTasks = $result['unassigned_count'] > 0;
}

function showManagerFunctions($action) {
    echo "<nav class='main-navigation'>";
    echo "<h2>Manager Dashboard</h2>";
    echo "<a href='navigation.php?action=add_project' class='nav-link" . ($action === 'add_project' ? '-active' : '') . "'>Add Project</a>";
    echo "<a href='navigation.php?action=allocate_project' class='nav-link" . ($action === 'allocate_project' ? '-active' : '') . "'>Allocate Team Leader</a>";
    echo "<a href='navigation.php?action=search' class='nav-link" . ($action === 'search' ? '-active' : '') . "'>Search</a>";
    echo "</nav>";
}

function showProjectLeaderFunctions($action, $hasUnassignedTasks) {
    echo "<nav class='main-navigation'>";
    echo "<h2>Project Leader Dashboard</h2>";
    echo "<a href='navigation.php?action=create_task' class='nav-link" . ($action === 'create_task' ? '-active' : '') . "'>Create Task</a>";
    echo "<a href='navigation.php?action=assign_team_members' class='nav-link" . ($action === 'assign_team_members' ? '-active' : '') . "' id='" . ($hasUnassignedTasks ? 'assigned' : '') . "'>Assign Team Members</a>";
    echo "<a href='navigation.php?action=search' class='nav-link" . ($action === 'search' ? '-active' : '') . "'>Search</a>";
    echo "</nav>";
}  

function showTeamMemberFunctions($action) {
    echo "<nav class='main-navigation'>";
    echo "<h2>Team Member Dashboard</h2>";
    echo "<a href='navigation.php?action=accept_task' class='nav-link" . ($action === 'accept_task' ? '-active' : '') . "'>Accept Task</a>";
    echo "<a href='navigation.php?action=update_task_progress' class='nav-link" . ($action === 'update_task_progress' ? '-active' : '') . "'>Update Task</a>";
    echo "<a href='navigation.php?action=search' class='nav-link" . ($action === 'search' ? '-active' : '') . "'>Search Task</a>";
    echo "</nav>";
}
?>

<section class="main-view">
    <section class="left-view">
        <?php
        if ($role === 'Manager') {
            showManagerFunctions($action);
        } elseif ($role === 'Project Leader') {
            showProjectLeaderFunctions($action, $hasUnassignedTasks);
        } elseif ($role === 'Team Member') {
            showTeamMemberFunctions($action);
        } else {
            echo "Invalid role.";
        }
        ?>
    </section>
    <section class="right-view">
        <nav>
        <?php
        if (isset($_GET['action'])) {
            $action = $_GET['action'];
            switch ($action) {
                case 'add_project':
                    include 'create_project.php';
                    break;
                case 'allocate_project':
                    include 'allocate_project.php';
                    break;
                case 'assign_leader':
                    include 'assign_leader.php';
                    break;
                case 'create_task':
                    include 'task_create.php';
                    break;
                case 'assign_team_members':
                    include 'assign_team_members.php';
                    break;
                case 'accept_task':
                    include 'task_accept.php';
                    break;
                case 'update_task_progress':
                    include 'update_task.php';
                    break;
                case 'search':
                    include 'task_search.php';
                    break;
            }
        } else {
            include 'task_search.php';
        }
        ?>
        </nav>
    </section>
</section>
<?php include "footer.html"; ?>
