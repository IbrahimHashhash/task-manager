<?php
require_once "db.php.inc.php";

if (isset($_GET['project_id'])) {
    $project_id = $_GET['project_id'];

    $sql_project = "SELECT * FROM projects WHERE project_id = :project_id";
    $stmt_project = $pdo->prepare($sql_project);
    $stmt_project->execute(['project_id' => $project_id]);
    $project = $stmt_project->fetch();

    $sql_leaders = "SELECT user_id, full_name FROM users WHERE role = 'Project Leader'";
    $stmt_leaders = $pdo->prepare($sql_leaders);
    $stmt_leaders->execute();
    $leaders = $stmt_leaders->fetchAll();

    $sql_documents = "SELECT * FROM project_documents WHERE project_id = :project_id";
    $stmt_documents = $pdo->prepare($sql_documents);
    $stmt_documents->execute(['project_id' => $project_id]);
    $documents = $stmt_documents->fetchAll();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_id = $_POST['project_id'];
    $team_leader_id = $_POST['team_leader_id'];

    if (empty($team_leader_id)) {
        echo "<p class='error'>Error: Please select a team leader.</p>";
        exit;
    }

    try {

$sql_update_project = "UPDATE projects SET project_leader_id = :team_leader_id WHERE project_id = :project_id";
$stmt_update_project = $pdo->prepare($sql_update_project);
$stmt_update_project->execute([
    'team_leader_id' => $team_leader_id,
    'project_id' => $project_id
]);

        echo "<p>Team Leader successfully allocated to Project {$project_id}.</p>";
        echo '<a href="dashboard.php">Return to Dashboard</a>';
        exit;
    } catch (Exception $e) {
        echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
        exit;
    }
}
?>

<?php if (!isset($project)): ?>
    <h2>Unassigned Projects</h2>
    <table>
        <tr>
            <th>Project ID</th>
            <th>Project Title</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Action</th>
        </tr>
        <?php
        $sql_unassigned_projects = "SELECT * FROM projects WHERE project_leader_id IS NULL";
        $stmt_unassigned = $pdo->prepare($sql_unassigned_projects);
        $stmt_unassigned->execute();
        $unassigned_projects = $stmt_unassigned->fetchAll();

        foreach ($unassigned_projects as $project) {
            echo "<tr>";
            echo "<td>" . $project['project_id'] . "</td>";
            echo "<td>" . $project['title'] . "</td>";
            echo "<td>" . $project['start_date'] . "</td>";
            echo "<td>" . $project['end_date'] . "</td>";
            echo "<td><a href='navigation.php?action=assign_leader&project_id=" . $project['project_id'] . "'>Allocate Project Leader</a></td>";
            echo "</tr>";
        }
        ?>
    </table>
<?php endif; ?>

