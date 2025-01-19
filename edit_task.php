<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$taskID = $_GET['id'];

// Fetch the task to edit
$stmt = $conn->prepare("SELECT * FROM Task WHERE taskID = ?");
$stmt->bind_param("i", $taskID);
$stmt->execute();
$task = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];
    $priority = $_POST['priority'];
    $projectID = $_POST['projectID'];
    $taskTypeID = $_POST['taskTypeID'];

    $stmt = $conn->prepare("UPDATE Task SET title = ?, description = ?, deadline = ?, priority = ?, projectID = ?, taskTypeID = ? WHERE taskID = ?");
    $stmt->bind_param("ssssiii", $title, $description, $deadline, $priority, $projectID, $taskTypeID, $taskID);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
    } else {
        $error = "Error updating task.";
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
    <title>Edit Task</title>
</head>
<body>
    <div class="form-container">
        <form method="POST">
            <h2>Edit Task</h2>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <input type="text" name="title" value="<?php echo $task['title']; ?>" required>
            <textarea name="description" required><?php echo $task['description']; ?></textarea>
            <input type="date" name="deadline" value="<?php echo $task['deadline']; ?>" required>
            <select name="priority" required>
                <option value="Low" <?php echo $task['priority'] == 'Low' ? 'selected' : ''; ?>>Low</option>
                <option value="Medium" <?php echo $task['priority'] == 'Medium' ? 'selected' : ''; ?>>Medium</option>
                <option value="High" <?php echo $task['priority'] == 'High' ? 'selected' : ''; ?>>High</option>
            </select>
            <select name="projectID" required>
                <option value="">Select Project</option>
                <?php while ($project = $projects->fetch_assoc()) { ?>
                    <option value="<?php echo $project['projectID']; ?>" <?php echo $project['projectID'] == $task['projectID'] ? 'selected' : ''; ?>>
                        <?php echo $project['name']; ?>
                    </option>
                <?php } ?>
            </select>
            <select name="taskTypeID" required>
                <option value="">Select Task Type</option>
                <?php while ($taskType = $taskTypes->fetch_assoc()) { ?>
                    <option value="<?php echo $taskType['taskTypeID']; ?>" <?php echo $taskType['taskTypeID'] == $task['taskTypeID'] ? 'selected' : ''; ?>>
                        <?php echo $taskType['typeName']; ?>
                    </option>
                <?php } ?>
            </select>
            <button type="submit">Update Task</button>
            <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
        </form>
    </div>
</body>
</html>
