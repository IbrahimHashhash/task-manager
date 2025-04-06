<h2>Create Project</h2>
<form action="add_project.php" method="post" enctype="multipart/form-data" id="project-form">
    <fieldset>
        <legend>Project Info</legend>
    <label for="project-id">Project ID</label>
    <input type="text" id="project-id" name="project_id" placeholder="e.g., ABCD-12345" pattern="[A-Z]{4}-\d{5}" required><br>

    <label for="project-title">Project Title</label>
    <input type="text" id="project-title" name="project_title" placeholder="Enter the project title" required><br>

    <label for="project-description">Project Description</label>
    <textarea id="project-description" name="project_description" placeholder="Enter project details" required></textarea><br>
    </fieldset>
     <fieldset>
        <legend>Customer Info</legend>
    <label for="customer-name">Customer Name</label>
    <input type="text" id="customer-name" name="customer_name" placeholder="Enter the customer's name" required><br>

    <label for="total-budget">Total Budget</label>
    <input type="number" id="total-budget" name="total_budget" placeholder="Enter the project budget" min="0" required><br>
    <label for="start-date">Start Date</label>
    <input type="date" id="start-date" name="start_date" required><br>

    <label for="end-date">End Date</label>
    <input type="date" id="end-date" name="end_date" required><br>
    </fieldset>
    <fieldset>
    <legend>Supporting Documents</legend><br>
    <input type="file" name="document_1" accept=".pdf,.docx,.png,.jpg">
    <input type="text" name="document_title_1" placeholder="Document Title"><br>
    <input type="file" name="document_2" accept=".pdf,.docx,.png,.jpg">
    <input type="text" name="document_title_2" placeholder="Document Title"><br>
    <input type="file" name="document_3" accept=".pdf,.docx,.png,.jpg">
    <input type="text" name="document_title_3" placeholder="Document Title"><br>
    <button type="submit">Add Project</button>
</fieldset>
</form>
