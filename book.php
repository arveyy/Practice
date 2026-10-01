<?php
require 'index.php';

// DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM Book WHERE bookID=$id");
    header("Location: book.php"); exit();
}

// ADD or UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['bookID']);
    $title = esc($_POST['bookTitle']);
    $copies = intval($_POST['bookCopies']);
    $authID = intval($_POST['authID']);

    if ($_POST['bookID'] == '') {
        $conn->query("INSERT INTO Book (bookTitle,bookCopies,authID) VALUES ('$title',$copies,$authID)");
    } else {
        $conn->query("UPDATE Book SET bookTitle='$title',bookCopies=$copies,authID=$authID WHERE bookID=$id");
    }
    header("Location: book.php"); exit();
}

// LOAD ONE RECORD FOR EDIT
$editRow = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $editRow = $conn->query("SELECT * FROM Book WHERE bookID=$id")->fetch_assoc();
}

// LIST OF AUTHORS for the dropdown
$authors = $conn->query("SELECT * FROM Author ORDER BY authLName");

// SEARCH or SHOW ALL (joined with Author so we can show the name)
$search = trim($_GET['search'] ?? '');
$base = "SELECT Book.*, Author.authLName, Author.authFName FROM Book
         LEFT JOIN Author ON Book.authID = Author.authID";
if ($search != '') {
    $like = esc($search);
    $result = $conn->query("$base WHERE bookTitle LIKE '%$like%'");
} else {
    $result = $conn->query("$base ORDER BY Book.bookID DESC");
}
?>
<!DOCTYPE html>
<html><head><title>Book Management</title><link rel="stylesheet" href="styles.css"></head>
<body>
<?php nav(); ?>
<h1>Book Records</h1>

<form method="GET">
    <input type="text" name="search" placeholder="Search title" value="<?= h($search) ?>">
    <button>Search</button> <a href="book.php">Reset</a>
</form>

<h2><?= $editRow ? "Edit Book" : "Add Book" ?></h2>
<form method="POST">
    <input type="hidden" name="bookID" value="<?= $editRow ? h($editRow['bookID']) : '' ?>">
    <input type="text" name="bookTitle" placeholder="Title" value="<?= $editRow ? h($editRow['bookTitle']) : '' ?>" required>
    <input type="number" name="bookCopies" placeholder="Copies" value="<?= $editRow ? h($editRow['bookCopies']) : '' ?>" required>
    <select name="authID" required>
        <option value="">-- Select Author --</option>
        <?php while ($a = $authors->fetch_assoc()) { ?>
            <option value="<?= $a['authID'] ?>" <?= ($editRow && $editRow['authID'] == $a['authID']) ? 'selected' : '' ?>>
                <?= h($a['authFName'] . ' ' . $a['authLName']) ?>
            </option>
        <?php } ?>
    </select>
    <button><?= $editRow ? "Update" : "Add" ?></button>
</form>

<table border="1" cellpadding="8">
    <tr><th>ID</th><th>Title</th><th>Copies</th><th>Author</th><th>Actions</th></tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?= $row['bookID'] ?></td>
        <td><?= h($row['bookTitle']) ?></td>
        <td><?= $row['bookCopies'] ?></td>
        <td><?= h($row['authFName'] . ' ' . $row['authLName']) ?></td>
        <td>
            <a href="book.php?edit=<?= $row['bookID'] ?>">Edit</a> |
            <a href="book.php?delete=<?= $row['bookID'] ?>" onclick="return confirm('Delete?')">Delete</a>
        </td>
    </tr>
    <?php } ?>
</table>
</body>
</html>