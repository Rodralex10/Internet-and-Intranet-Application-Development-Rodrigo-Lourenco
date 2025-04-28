<?php
// Database connection
$servername = "db";
$username = "vaiicko_user";
$dbname = "vaiicko_db";
$password = "dtb456";

try {
    $dbh = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $name = $_POST['name'];
    $author_name = $_POST['author_name'];
    $issue_date = $_POST['issue_date'];
    $number_pages = $_POST['number_pages'];
    $genre = $_POST['genre'];

    $stmt = $dbh->prepare("INSERT INTO books (name, author_name, issue_date, number_pages, genre) VALUES (?, ?, ?, ?, ?)");
    $stmt->bindParam(1, $name);
    $stmt->bindParam(2, $author_name);
    $stmt->bindParam(3, $issue_date);
    $stmt->bindParam(4, $number_pages);
    $stmt->bindParam(5, $genre);

    if ($stmt->execute()) {
        header("Location: index.php?success=1");
        exit();
    } else {
        header("Location: index.php?error=1");
        exit();
    }
}

// Handle sorting
$order = isset($_GET['order']) ? $_GET['order'] : 'id';
$direction = isset($_GET['direction']) && $_GET['direction'] == 'desc' ? 'DESC' : 'ASC';
$allowed_columns = ['id', 'name', 'author_name', 'issue_date', 'number_pages', 'genre'];

if (!in_array($order, $allowed_columns)) {
    $order = 'id';
}

$stmt = $dbh->prepare("SELECT id, name, author_name, issue_date, number_pages, genre FROM books ORDER BY $order $direction");
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
$nextDirection = $direction === 'ASC' ? 'desc' : 'asc';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Books List</title>
    <link rel="stylesheet" href="style.css"> <!-- CSS REF -->
</head>
<body>

<h1>Books List</h1>

<?php
if (isset($_GET['success'])) {
    echo '<p>Book added successfully!</p>';
} elseif (isset($_GET['error'])) {
    echo '<p>Error adding book. Try again.</p>';
}
?>

<?php if (count($books) > 0): ?>
    <table border="1">
        <tr>
            <th><a href="?order=id&direction=<?= $nextDirection ?>">ID</a></th>
            <th><a href="?order=name&direction=<?= $nextDirection ?>">Book Name</a></th>
            <th><a href="?order=author_name&direction=<?= $nextDirection ?>">Author Name</a></th>
            <th><a href="?order=issue_date&direction=<?= $nextDirection ?>">Issue Date</a></th>
            <th><a href="?order=number_pages&direction=<?= $nextDirection ?>">Number of Pages</a></th>
            <th><a href="?order=genre&direction=<?= $nextDirection ?>">Genre</a></th>
        </tr>
        <?php foreach ($books as $book): ?>
            <tr>
                <td><?= htmlspecialchars($book['id']) ?></td>
                <td><?= htmlspecialchars($book['name']) ?></td>
                <td><?= htmlspecialchars($book['author_name']) ?></td>
                <td><?= htmlspecialchars($book['issue_date']) ?></td>
                <td><?= htmlspecialchars($book['number_pages']) ?></td>
                <td><?= htmlspecialchars($book['genre']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No books available.</p>
<?php endif; ?>

<!-- Include form of html-->
<?php include 'form.html'; ?>

</body>
</html>
