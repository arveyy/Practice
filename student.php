<?php
require 'index.php';

// DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM Student WHERE studID=$id");
    header("Location: student.php"); exit();
}

// ADD or UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['studID']);
    $fname = esc($_POST['studFName']);
    $mname = esc($_POST['studMName']);
    $lname = esc($_POST['studLName']);

    if ($_POST['studID'] == '') {
        $conn->query("INSERT INTO Student (studFName,studMName,studLName) VALUES ('$fname','$mname','$lname')");
    } else {
        $conn->query("UPDATE Student SET studFName='$fname',studMName='$mname',studLName='$lname' WHERE studID=$id");
    }
    header("Location: student.php"); exit();
}

// LOAD ONE RECORD FOR EDIT
$editRow = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $editRow = $conn->query("SELECT * FROM Student WHERE studID=$id")->fetch_assoc();
}

// SEARCH or SHOW ALL
$search = trim($_GET['search'] ?? '');
if ($search != '') {
    $like = esc($search);
    $result = $conn->query("SELECT * FROM Student WHERE CONCAT(studFName,' ',studMName,' ',studLName) LIKE '%$like%'");
} else {
    $result = $conn->query("SELECT * FROM Student ORDER BY studID DESC");
}
?>
<!DOCTYPE html>
<html><head><title>Student Management</title><link rel="stylesheet" href="styles.css"></head>
<body>
<?php nav(); ?>
<h1>Student Records</h1>

<form method="GET">
    <input type="text" name="search" placeholder="Search name" value="<?= h($search) ?>">
    <button>Search</button> <a href="student.php">Reset</a>
</form>

<h2><?= $editRow ? "Edit Student" : "Add Student" ?></h2>
<form method="POST">
    <input type="hidden" name="studID" value="<?= $editRow ? h($editRow['studID']) : '' ?>">
    <input type="text" name="studFName" placeholder="First Name" value="<?= $editRow ? h($editRow['studFName']) : '' ?>" required>
    <input type="text" name="studMName" placeholder="Middle Name" value="<?= $editRow ? h($editRow['studMName']) : '' ?>">
    <input type="text" name="studLName" placeholder="Last Name" value="<?= $editRow ? h($editRow['studLName']) : '' ?>" required>
    <button><?= $editRow ? "Update" : "Add" ?></button>
</form>

<table border="1" cellpadding="8">
    <tr><th>ID</th><th>First Name</th><th>Middle Name</th><th>Last Name</th><th>Actions</th></tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?= $row['studID'] ?></td>
        <td><?= h($row['studFName']) ?></td>
        <td><?= h($row['studMName']) ?></td>
        <td><?= h($row['studLName']) ?></td>
        <td>
            <a href="student.php?edit=<?= $row['studID'] ?>">Edit</a> |
            <a href="student.php?delete=<?= $row['studID'] ?>" onclick="return confirm('Delete?')">Delete</a>
        </td>
    </tr>
    <?php } ?>
</table>
</body>
</html>