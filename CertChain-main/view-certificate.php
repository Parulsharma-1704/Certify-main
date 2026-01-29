<?php
include 'db-config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "
        SELECT c.*, r.name AS recipient_name 
        FROM certificates c 
        JOIN recipients r ON c.recipient_id = r.id 
        WHERE c.id = $id
    ";
    $result = $conn->query($query);
    if ($result->num_rows === 1) {
        $certificate = $result->fetch_assoc();
    } else {
        die("Certificate not found.");
    }
} else {
    die("Invalid request.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Certificate</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #eef5fc;
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 40px;
            animation: fadeIn 0.6s ease-out;
        }

        .certificate-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .certificate-header h2 {
            color: #007bff;
            margin-bottom: 5px;
        }

        .certificate-body {
            padding: 20px;
            background: #f9fbff;
            border-radius: 10px;
            border: 1px dashed #cdddf5;
        }

        .certificate-field {
            margin-bottom: 15px;
        }

        .certificate-field label {
            font-weight: bold;
            display: block;
            color: #333;
        }

        .certificate-field span {
            display: block;
            font-size: 16px;
            color: #555;
            margin-top: 5px;
        }

        .status-valid {
            color: green;
            font-weight: bold;
        }

        .status-pending {
            color: orange;
            font-weight: bold;
        }

        .certificate-footer {
            text-align: center;
            margin-top: 30px;
        }

        .certificate-footer a {
            text-decoration: none;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border-radius: 6px;
            margin: 0 10px;
            transition: background 0.3s ease;
        }

        .certificate-footer a:hover {
            background: #0056b3;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="certificate-header">
        <h2>Certificate Details</h2>
        <p><i class="fas fa-certificate fa-lg"></i></p>
    </div>

    <div class="certificate-body">
        <div class="certificate-field">
            <label>Certificate ID</label>
            <span><?php echo htmlspecialchars($certificate['certificate_id']); ?></span>
        </div>

        <div class="certificate-field">
            <label>Title</label>
            <span><?php echo htmlspecialchars($certificate['title']); ?></span>
        </div>

        <div class="certificate-field">
            <label>Recipient Name</label>
            <span><?php echo htmlspecialchars($certificate['recipient_name']); ?></span>
        </div>

        <div class="certificate-field">
            <label>Issue Date</label>
            <span><?php echo htmlspecialchars($certificate['issue_date']); ?></span>
        </div>

        <div class="certificate-field">
            <label>Status</label>
            <span class="<?php echo $certificate['status'] === 'verified' ? 'status-valid' : 'status-pending'; ?>">
                <?php echo ucfirst($certificate['status']); ?>
            </span>
        </div>
    </div>

    <div class="certificate-footer">
        <a href="all-certificates.php"><i class="fas fa-arrow-left"></i> Back</a>
        <a href="download.php?id=<?php echo $certificate['id']; ?>"><i class="fas fa-download"></i> Download</a>
        <a href="share.php?id=<?php echo $certificate['id']; ?>"><i class="fas fa-share-alt"></i> Share</a>
    </div>
</div>

</body>
</html>
