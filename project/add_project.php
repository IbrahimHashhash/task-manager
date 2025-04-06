<?php
include "header.php";
require_once "db.php.inc.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_id = $_POST['project_id'];
    $project_title = $_POST['project_title'];
    $project_description = $_POST['project_description'];
    $customer_name = $_POST['customer_name'];
    $total_budget = $_POST['total_budget'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $documents = [];
    for ($i = 1; $i <= 3; $i++) {
        if (!empty($_FILES["document_$i"]['name'])) {
            $documents[] = [
                'file' => $_FILES["document_$i"],
                'title' => $_POST["document_title_$i"]
            ];
        }
    }

    $errors = [];

    if (!preg_match('/^[A-Z]{4}-\d{5}$/', $project_id)) {
        $errors[] = "Project ID must start with 4 uppercase letters, a dash (-), and 5 digits.";
    }

    if ($total_budget <= 0) {
        $errors[] = "Budget must be a positive numeric value.";
    }

    if (strtotime($end_date) < strtotime($start_date)) {
        $errors[] = "End Date must be later than Start Date.";
    }

    foreach ($documents as $index => $doc) {
        $file = $doc['file'];
        $title = $doc['title'];

        if (empty($title)) {
            $errors[] = "Title for Document " . ($index + 1) . " is required.";
        }

        $allowed_types = ['pdf', 'docx', 'png', 'jpg'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_ext, $allowed_types)) {
            $errors[] = "Invalid file type for Document " . ($index + 1) . ". Only PDF, DOCX, PNG, and JPG are allowed.";
        }

        if ($file['size'] > 2 * 1024 * 1024) { // 2MB limit
            $errors[] = "File size for Document " . ($index + 1) . " exceeds 2MB.";
        }
    }

    if (!empty($errors)) {
        echo "<div class='error_container'>";
        echo "<h2>Error</h2>";
        foreach ($errors as $error) {
            echo "<p class='error'>$error</p>";
        }
        echo '<a href="navigation.php?action=add_project">Go back</a>';
        echo "</div>";
        exit;
    }

    try {
        $sql_project = "INSERT INTO projects (project_id, title, description, customer_name, budget, start_date, end_date) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_project = $pdo->prepare($sql_project);
        $stmt_project->execute([
            $project_id,
            $project_title,
            $project_description,
            $customer_name,
            $total_budget,
            $start_date,
            $end_date
        ]);

        $target_dir = "documents/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        foreach ($documents as $doc) {
            $file = $doc['file'];
            $title = $doc['title'];

            $file_name = uniqid() . "_" . basename($file['name']);
            $target_file = $target_dir . $file_name;

            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                $sql_doc = "INSERT INTO project_documents (project_id, title, file_path) VALUES (?, ?, ?)";
                $stmt_doc = $pdo->prepare($sql_doc);
                $stmt_doc->execute([
                    $project_id,
                    $title,
                    $target_file
                ]);
            } else {
                echo "<p class='error'>Failed to upload file: " . $file['name'] . "</p>";
            }
        }
        echo "<div class='success_container'>";
        echo "<h2>Success</h2>";
        echo "<p class='success'>Project added successfully!</p>";
        echo '<a href="navigation.php?action=add_project">Return to Project List</a>';
        echo "</div>";
    } catch (PDOException $e) {
        echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
    }
}
?>
<?php include "footer.html"; ?>
