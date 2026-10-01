<?php
require 'index.php';

// DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM Author WHERE authID=$id");
    header("Location: author.php"); exit();
}

// ADD or UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['authID']);
    $lname = esc($_POST['authLName']);
    $fname = esc($_POST['authFName']);
    $mname = esc($_POST['authMName']);

    if ($_POST['authID'] == '') {
        $conn->query("INSERT INTO Author (authLName,authFName,authMName) VALUES ('$lname','$fname','$mname')");
    } else {
        $conn->query("UPDATE Author SET authLName='$lname',authFName='$fname',authMName='$mname' WHERE authID=$id");
    }
    header("Location: author.php"); exit();
}

// LOAD ONE RECORD FOR EDIT
$editRow = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $editRow = $conn->query("SELECT * FROM Author WHERE authID=$id")->fetch_assoc();
}

// SEARCH or SHOW ALL
$search = trim($_GET['search'] ?? '');
if ($search != '') {
    $like = esc($search);
    $result = $conn->query("SELECT * FROM Author WHERE CONCAT(authFName,' ',authMName,' ',authLName) LIKE '%$like%'");
} else {
    $result = $conn->query("SELECT * FROM Author ORDER BY authID DESC");
}
?>
<!DOCTYPE html>
<html><head><title>Author Management</title><link rel="stylesheet" href="styles.css"></head>
<body>
<?php nav(); ?>
<h1>Author Records</h1>

<form method="GET">
    <input type="text" name="search" placeholder="Search name" value="<?= h($search) ?>">
    <button>Search</button> <a href="author.php">Reset</a>
</form>

<h2><?= $editRow ? "Edit Author" : "Add Author" ?></h2>
<form method="POST">
    <input type="hidden" name="authID" value="<?= $editRow ? h($editRow['authID']) : '' ?>">
    <input type="text" name="authLName" placeholder="Last Name" value="<?= $editRow ? h($editRow['authLName']) : '' ?>" required>
    <input type="text" name="authFName" placeholder="First Name" value="<?= $editRow ? h($editRow['authFName']) : '' ?>" required>
    <input type="text" name="authMName" placeholder="Middle Name" value="<?= $editRow ? h($editRow['authMName']) : '' ?>">
    <button><?= $editRow ? "Update" : "Add" ?></button>
</form>

<table border="1" cellpadding="8">
    <tr><th>ID</th><th>Last Name</th><th>First Name</th><th>Middle Name</th><th>Actions</th></tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?= $row['authID'] ?></td>
        <td><?= h($row['authLName']) ?></td>
        <td><?= h($row['authFName']) ?></td>
        <td><?= h($row['authMName']) ?></td>
        <td>
            <a href="author.php?edit=<?= $row['authID'] ?>">Edit</a> |
            <a href="author.php?delete=<?= $row['authID'] ?>" onclick="return confirm('Delete?')">Delete</a>
        </td>
    </tr>
    <?php } ?>
</table>
</body>
</html>