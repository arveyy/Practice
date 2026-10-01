<?php
require 'index.php';

// BORROW (create a new BorrowedBook record)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $studID = intval($_POST['studID']);
    $bookID = intval($_POST['bookID']);
    $conn->query("INSERT INTO BorrowedBook (studID,bookID,status) VALUES ($studID,$bookID,'Borrowed')");
    header("Location: borrow.php"); exit();
}

// RETURN (update status, keep the record)
if (isset($_GET['return'])) {
    $id = intval($_GET['return']);
    $conn->query("UPDATE BorrowedBook SET status='Returned' WHERE borrowID=$id");
    header("Location: borrow.php"); exit();
}

$students = $conn->query("SELECT * FROM Student ORDER BY studLName");

// Availability = total copies minus copies still out (status = Borrowed)
$availSql = "SELECT Book.bookID, Book.bookTitle, Book.bookCopies,
            (Book.bookCopies - COUNT(CASE WHEN BorrowedBook.status='Borrowed' THEN 1 END)) AS available
            FROM Book LEFT JOIN BorrowedBook ON Book.bookID = BorrowedBook.bookID
            GROUP BY Book.bookID, Book.bookTitle, Book.bookCopies";
$availableBooks = $conn->query($availSql);
$availableBooksForForm = $conn->query("$availSql HAVING available > 0"); // 2nd copy: a result can only loop once

$borrowedList = $conn->query("SELECT BorrowedBook.*, Student.studFName, Student.studLName, Book.bookTitle
    FROM BorrowedBook JOIN Student ON BorrowedBook.studID=Student.studID JOIN Book ON BorrowedBook.bookID=Book.bookID
    WHERE status='Borrowed' ORDER BY borrowID DESC");

$returnedList = $conn->query("SELECT BorrowedBook.*, Student.studFName, Student.studLName, Book.bookTitle
    FROM BorrowedBook JOIN Student ON BorrowedBook.studID=Student.studID JOIN Book ON BorrowedBook.bookID=Book.bookID
    WHERE status='Returned' ORDER BY borrowID DESC");
?>
<!DOCTYPE html>
<html><head><title>Book Borrowing</title><link rel="stylesheet" href="styles.css"></head>
<body>
<?php nav(); ?>
<h1>Book Borrowing</h1>

<h2>Book Availability</h2>
<table border="1" cellpadding="8">
    <tr><th>Title</th><th>Total Copies</th><th>Available</th></tr>
    <?php while ($row = $availableBooks->fetch_assoc()) { ?>
    <tr><td><?= h($row['bookTitle']) ?></td><td><?= $row['bookCopies'] ?></td><td><?= $row['available'] ?></td></tr>
    <?php } ?>
</table>

<h2>Borrow a Book</h2>
<form method="POST">
    <select name="studID" required>
        <option value="">-- Select Student --</option>
        <?php while ($s = $students->fetch_assoc()) { ?>
            <option value="<?= $s['studID'] ?>"><?= h($s['studFName'] . ' ' . $s['studLName']) ?></option>
        <?php } ?>
    </select>
    <select name="bookID" required>
        <option value="">-- Select Book --</option>
        <?php while ($b = $availableBooksForForm->fetch_assoc()) { ?>
            <option value="<?= $b['bookID'] ?>"><?= h($b['bookTitle']) ?> (<?= $b['available'] ?> available)</option>
        <?php } ?>
    </select>
    <button>Borrow</button>
</form>

<h2>Currently Borrowed</h2>
<table border="1" cellpadding="8">
    <tr><th>Student</th><th>Book</th><th>Status</th><th>Action</th></tr>
    <?php while ($row = $borrowedList->fetch_assoc()) { ?>
    <tr>
        <td><?= h($row['studFName'] . ' ' . $row['studLName']) ?></td>
        <td><?= h($row['bookTitle']) ?></td>
        <td><?= $row['status'] ?></td>
        <td><a href="borrow.php?return=<?= $row['borrowID'] ?>">Return</a></td>
    </tr>
    <?php } ?>
</table>

<h2>Returned Books</h2>
<table border="1" cellpadding="8">
    <tr><th>Student</th><th>Book</th><th>Status</th></tr>
    <?php while ($row = $returnedList->fetch_assoc()) { ?>
    <tr>
        <td><?= h($row['studFName'] . ' ' . $row['studLName']) ?></td>
        <td><?= h($row['bookTitle']) ?></td>
        <td><?= $row['status'] ?></td>
    </tr>
    <?php } ?>
</table>
</body>
</html>