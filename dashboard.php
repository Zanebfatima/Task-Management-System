<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];
$query = "SELECT t.*, p.name AS projectName, tt.typeName AS taskType FROM Task t 
          JOIN Project p ON t.projectID = p.projectID 
          JOIN TaskType tt ON t.taskTypeID = tt.taskTypeID";

if (isset($_GET['priority'])) {
    $priority = $_GET['priority'];
    $query .= " WHERE t.priority = '$priority'";
}

$tasks = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    <title>Dashboard</title>
</head>
<body>
    <div class="dashboard">
        <h1>Welcome, <?php echo $user['username']; ?>!</h1>
        <a href="logout.php" class="logout">Logout</a>
        <a href="add_task.php" class="add-task">Add Task</a>

        <form method="GET">
            <label for="priority">Filter by Priority:</label>
            <select name="priority" id="priority">
                <option value="">All</option>
                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High">High</option>
            </select>
            <button type="submit">Filter</button>
        </form>

        <div class="task-table">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Priority</th>
                        <th>Deadline</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($task = $tasks->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $task['title']; ?></td>
                            <td><?php echo $task['description']; ?></td>
                            <td><?php echo $task['priority']; ?></td>
                            <td><?php echo $task['deadline']; ?></td>
                            <td>
                                <a href="edit_task.php?id=<?php echo $task['taskID']; ?>">Edit</a>
                                <a href="delete_task.php?id=<?php echo $task['taskID']; ?>">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

