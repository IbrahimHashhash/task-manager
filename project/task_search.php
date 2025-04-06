<?php
require_once 'db.php.inc.php';

if (!isset($_SESSION['user_id'])) {
    echo "Please log in to access this page.";
    exit();
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
$filter_conditions = '';
$res = [];
$order_clause = '';

if ($role == 'Manager') {
} elseif ($role == 'Project Leader') {
    $filter_conditions .= " AND p.project_leader_id = :user_id";
    $res[':user_id'] = $user_id;
} elseif ($role == 'Team Member') {
    $filter_conditions .= " AND ta.user_id = :user_id";
    $res[':user_id'] = $user_id;
}

if (isset($_POST['search'])) {
    if (!empty($_POST['priority'])) {
        $filter_conditions .= " AND t.priority = :priority";
        $res[':priority'] = $_POST['priority'];
    }
    if (!empty($_POST['status'])) {
        $filter_conditions .= " AND t.status = :status";
        $res[':status'] = $_POST['status'];
    }
    if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
        $filter_conditions .= " AND t.due_date BETWEEN :start_date AND :end_date";
        $res[':start_date'] = $_POST['start_date'];
        $res[':end_date'] = $_POST['end_date'];
    }
    if (!empty($_POST['project'])) {
        $filter_conditions .= " AND t.project_id = :project_id";
        $res[':project_id'] = $_POST['project'];
    }
}

$allowed_sort_columns = ['task_id', 'task_name', 'project_name', 'status', 'priority', 'start_date', 'due_date', 'progress'];
$sort_column = isset($_GET['sort']) && in_array($_GET['sort'], $allowed_sort_columns) ? $_GET['sort'] : 'task_id';
$sort_direction = isset($_GET['direction']) && strtolower($_GET['direction']) === 'desc' ? 'DESC' : 'ASC';
$order_clause = " ORDER BY $sort_column $sort_direction";

$sql_search = "
    SELECT DISTINCT 
        t.task_id, 
        t.task_name, 
        p.title AS project_name, 
        t.start_date, 
        t.due_date, 
        t.status, 
        t.priority, 
        t.progress 
    FROM tasks t
    JOIN projects p ON t.project_id = p.project_id
    LEFT JOIN task_assignments ta ON t.task_id = ta.task_id
    WHERE 1=1 $filter_conditions $order_clause";

$stmt_search = $pdo->prepare($sql_search);
foreach ($res as $key => $value) {
    $stmt_search->bindValue($key, $value);
}
$stmt_search->execute();
$tasks = $stmt_search->fetchAll();
?>

<form method="post" action="" class="search">
    <label for="priority">Priority:</label>
    <select name="priority" id="priority">
        <option value="">All</option>
        <option value="Low">Low</option>
        <option value="Medium">Medium</option>
        <option value="High">High</option>
    </select>

    <label for="status">Status:</label>
    <select name="status" id="status">
        <option value="">All</option>
        <option value="Pending">Pending</option>
        <option value="In Progress">In Progress</option>
        <option value="Completed">Completed</option>
    </select>

    <label for="start_date">Start Date:</label>
    <input type="date" name="start_date" id="start_date">

    <label for="end_date">End Date:</label>
    <input type="date" name="end_date" id="end_date">

    <label for="project">Project:</label>
    <select name="project" id="project">
        <option value="">All Projects</option>
        <?php
        if ($role == 'Team Member') {
            $sql_projects = "
                SELECT DISTINCT p.project_id, p.title 
                FROM projects p
                JOIN tasks t ON p.project_id = t.project_id
                JOIN task_assignments ta ON t.task_id = ta.task_id
                WHERE ta.user_id = :user_id";
            $stmt_projects = $pdo->prepare($sql_projects);
            $stmt_projects->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt_projects->execute();
            $projects = $stmt_projects->fetchAll();

            foreach ($projects as $project) {
                echo "<option value='" . $project['project_id'] . "'>" . $project['title'] . "</option>";
            }
        } else {
            $sql_all_projects = "SELECT project_id, title FROM projects";
            $stmt_all_projects = $pdo->query($sql_all_projects);
            $projects = $stmt_all_projects->fetchAll();

            foreach ($projects as $project) {
                echo "<option value='" . $project['project_id'] . "'>" . $project['title'] . "</option>";
            }
        }
        ?>
    </select>

    <button type="submit" name="search">Search</button>
</form>

<?php if (isset($tasks) && !empty($tasks)): ?>
    <table class="task-table">
        <thead>
            <tr>
                <th><a href="?sort=task_id&direction=<?php echo $sort_column === 'task_id' && $sort_direction === 'ASC' ? 'desc' : 'asc'; ?>">Task ID</a></th>
                <th><a href="?sort=task_name&direction=<?php echo $sort_column === 'task_name' && $sort_direction === 'ASC' ? 'desc' : 'asc'; ?>">Title</a></th>
                <th><a href="?sort=project_name&direction=<?php echo $sort_column === 'project_name' && $sort_direction === 'ASC' ? 'desc' : 'asc'; ?>">Project</a></th>
                <th><a href="?sort=status&direction=<?php echo $sort_column === 'status' && $sort_direction === 'ASC' ? 'desc' : 'asc'; ?>">Status</a></th>
                <th><a href="?sort=priority&direction=<?php echo $sort_column === 'priority' && $sort_direction === 'ASC' ? 'desc' : 'asc'; ?>">Priority</a></th>
                <th><a href="?sort=start_date&direction=<?php echo $sort_column === 'start_date' && $sort_direction === 'ASC' ? 'desc' : 'asc'; ?>">Start Date</a></th>
                <th><a href="?sort=due_date&direction=<?php echo $sort_column === 'due_date' && $sort_direction === 'ASC' ? 'desc' : 'asc'; ?>">Due Date</a></th>
                <th><a href="?sort=progress&direction=<?php echo $sort_column === 'progress' && $sort_direction === 'ASC' ? 'desc' : 'asc'; ?>">Completion %</a></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tasks as $task): ?>
                <tr id="priority-<?php echo strtolower($task['priority']); ?>">
                    <td><a id="task_id" href="task_details.php?task_id=<?php echo $task['task_id']; ?>"><?php echo $task['task_id']; ?></a></td>
                    <td><?php echo $task['task_name']; ?></td>
                    <td><?php echo $task['project_name']; ?></td>
                    <td class="status-<?php echo strtolower(str_replace(' ', '-', $task['status'])); ?>"><?php echo $task['status']; ?></td>
                    <td><?php echo $task['priority']; ?></td>
                    <td><?php echo $task['start_date']; ?></td>
                    <td><?php echo $task['due_date']; ?></td>
                    <td><?php echo $task['progress']; ?>%</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No tasks found.</p>
<?php endif; ?>