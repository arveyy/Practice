<?php
// Shared connection + helpers, used by every page (including this one)
$conn = new mysqli("localhost", "root", "", "crud_test");
if ($conn->connect_error) die("Connect failed: " . $conn->connect_error);

function h($s) { return htmlspecialchars($s); }               // safe to print
function esc($s) { global $conn; return $conn->real_escape_string($s); } // safe for SQL

function nav() {
    echo '<nav>
        <a href="author.php">Authors</a> |
        <a href="student.php">Students</a> |
        <a href="book.php">Books</a> |
        <a href="borrow.php">Borrow / Return</a>
    </nav>';
}

// Only show the landing page HTML when index.php itself is opened,
// not when another page does require 'index.php' just for the connection above.
if (basename($_SERVER['SCRIPT_NAME']) == 'index.php') {
?>
<!DOCTYPE html>
<html><head><title>Library System</title><link rel="stylesheet" href="styles.css"></head>
<body>
<?php nav(); ?>
<h1>Simple Library System</h1>
<p>Pick a module above. Authors, Students, and Books each have Add / Search / Edit / Delete. Borrow / Return handles borrowing.</p>
</body>
</html>
<?php
}
?>