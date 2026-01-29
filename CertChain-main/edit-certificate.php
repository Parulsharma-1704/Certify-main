<?php
include 'db-config.php';

// Check if ID is passed
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $certificateId = $_GET['id'];

    // Fetch certificate with recipient name
    $stmt = $conn->prepare("
        SELECT c.*, r.name AS recipient_name 
        FROM certificates c 
        JOIN recipients r ON c.recipient_id = r.id 
        WHERE c.id = ?
    ");
    $stmt->bind_param("i", $certificateId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Certificate not found.";
        exit;
    }

    $certificate = $result->fetch_assoc();

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $issue_date = $_POST['issue_date'];
        $status = $_POST['status'];
        $recipient_id = $_POST['recipient_id'];

        $update = $conn->prepare("UPDATE certificates SET title=?, issue_date=?, status=?, recipient_id=? WHERE id=?");
        $update->bind_param("sssii", $title, $issue_date, $status, $recipient_id, $certificateId);

        if ($update->execute()) {
            header("Location: view-certificate.php?id=" . $certificateId);
            exit;
        } else {
            echo "Failed to update certificate.";
        }
    }

    // Fetch recipients for dropdown
    $recipients = $conn->query("SELECT id, name FROM recipients");
} else {
    echo "Invalid certificate ID.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Certificate</title>
    <link rel="stylesheet" href="styles.css"> <!-- Your styles here -->
    <style>
        form {
            max-width: 600px;
            margin: 30px auto;
            padding: 25px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        input, select {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }
        label {
            margin-top: 15px;
            font-weight: bold;
        }
        button {
            margin-top: 20px;
            padding: 10px 20px;
        }
    </style>
</head>
<body>

<form method="POST">
    <h2>Edit Certificate</h2>
    <label>Title:</label>
    <input type="text" name="title" value="<?php echo htmlspecialchars($certificate['title']); ?>" required>

    <label>Issue Date:</label>
    <input type="date" name="issue_date" value="<?php echo $certificate['issue_date']; ?>" required>

    <label>Status:</label>
    <select name="status" required>
        <option value="pending" <?php if ($certificate['status'] === 'pending') echo 'selected'; ?>>Pending</option>
        <option value="verified" <?php if ($certificate['status'] === 'verified') echo 'selected'; ?>>Verified</option>
    </select>

    <label>Recipient:</label>
    <select name="recipient_id" required>
        <?php while ($row = $recipients->fetch_assoc()): ?>
            <option value="<?php echo $row['id']; ?>" <?php if ($row['id'] == $certificate['recipient_id']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($row['name']); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <button type="submit">Update Certificate</button>
</form>

</body>
</html>
