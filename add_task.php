<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];
    $priority = $_POST['priority'];
    $projectID = $_POST['projectID'];
    $taskTypeID = $_POST['taskTypeID'];

    $stmt = $conn->prepare("INSERT INTO Task (title, description, deadline, priority, projectID, taskTypeID) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssii", $title, $description, $deadline, $priority, $projectID, $taskTypeID);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
    } else {
        $error = "Error adding task.";
    }
}

// Fetch projects and task types for the form
$projects = $conn->query("SELECT * FROM Project");
$taskTypes = $conn->query("SELECT * FROM TaskType");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    <title>Add Task</title>
</head>
<body>
    <div class="form-container">
        <form method="POST">
            <h2>Add New Task</h2>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <input type="text" name="title" placeholder="Task Title" required>
            <textarea name="description" placeholder="Task Description" required></textarea>
            <input type="date" name="deadline" required>
            <select name="priority" required>
                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High">High</option>
            </select>
            <select name="projectID" required>
                <option value="">Select Project</option>
                <?php while ($project = $projects->fetch_assoc()) { ?>
                    <option value="<?php echo $project['projectID']; ?>"><?php echo $project['name']; ?></option>
                <?php } ?>
            </select>
            <select name="taskTypeID" required>
                <option value="">Select Task Type</option>
                <?php while ($taskType = $taskTypes->fetch_assoc()) { ?>
                    <option value="<?php echo $taskType['taskTypeID']; ?>"><?php echo $taskType['typeName']; ?></option>
                <?php } ?>
            </select>
            <button type="submit">Add Task</button>
            <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
        </form>
    </div>
</body>
</html>
