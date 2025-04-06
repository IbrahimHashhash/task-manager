<?php
require_once "db.php.inc.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_id = $_POST['project_id'];
    $leader_id = $_POST['leader_id'];

    $sql = "UPDATE projects SET project_leader_id = :leader_id WHERE project_id = :project_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'leader_id' => $leader_id,
        'project_id' => $project_id
    ]);
    include "header.php";
    echo "<div class='success_container'>";
    echo "<h2>Success</h2>";
    echo "<p class='success'>Leader assigned successfully!</p>";
    echo '<a href="navigation.php?action=allocate_project">Return to Project List</a>';
    echo "</div>";
    include "footer.html";
    exit;
}

if (isset($_GET['project_id'])) {
    $project_id = $_GET['project_id'];

    $sql_project = "SELECT * FROM projects WHERE project_id = :project_id";
    $stmt_project = $pdo->prepare($sql_project);
    $stmt_project->execute(['project_id' => $project_id]);
    $project = $stmt_project->fetch();

    if (!$project) {
        echo "<p class='error'>Error: Project not found.</p>";
        exit;
    }

    $sql_leaders = "SELECT user_id, full_name FROM users WHERE role = 'Project Leader'";
    $stmt_leaders = $pdo->prepare($sql_leaders);
    $stmt_leaders->execute();
    $leaders = $stmt_leaders->fetchAll();

    $sql_documents = "SELECT * FROM project_documents WHERE project_id = :project_id";
    $stmt_documents = $pdo->prepare($sql_documents);
    $stmt_documents->execute(['project_id' => $project_id]);
    $documents = $stmt_documents->fetchAll();
} else {
    echo "<p class='error'>Error: No project selected for allocation.</p>";
    exit;
}
$icon_mapping = [
    'pdf' => 'pdfIcon.png',
    'docx' => 'docxIcon.png',
    'png' => 'imageIcon.png',
];
?>

<h2>Allocate Project Leader to Project</h2>
<form action="assign_leader.php" method="post" id="allocate-project-leader">
    <fieldset>
        <legend>Project Details</legend>
        <p><strong>Project ID:</strong> <?= $project['project_id'] ?></p>
        <p><strong>Title:</strong> <?= $project['title'] ?></p>
        <p><strong>Start Date:</strong> <?= $project['start_date'] ?></p>
        <p><strong>End Date:</strong> <?= $project['end_date'] ?></p>
    </fieldset>

    <fieldset>
        <legend>Select Project Leader</legend>
        <input type="hidden" name="project_id" value="<?= $project['project_id'] ?>">
        <label for="project_leader">Project Leader:</label>
        <select name="leader_id" id="project_leader" required>
            <option value="">Select a Project Leader</option>
            <?php foreach ($leaders as $leader): ?>
                <option value="<?= $leader['user_id'] ?>">
                    <?= $leader['full_name'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Allocate Project Leader</button>
        <a href="navigation.php?action=allocate_project" id="cancel">Cancel</a>
    </fieldset>
</form>

<h2>Project Documents</h2>
<div class="doc-container">

<?php foreach ($documents as $doc): ?>
    <?php
$file_path = $doc['file_path'];
$file_name = $doc['title'];
$file_parts = explode('.', $file_path);
$file_ext = strtolower(end($file_parts));
$icon = isset($icon_mapping[$file_ext]) ? $icon_mapping[$file_ext] : 'default_icon.png';
$icon_url = 'images/' . $icon;
?>
<a href="documents/<?php echo basename($file_path); ?>" target="_blank">
    <img src="<?php echo $icon_url; ?>" alt="<?php echo $file_ext; ?>" class="doc-icon"> 
    <?php echo $file_name; ?>
</a><br>
<?php endforeach; ?>
</div>

