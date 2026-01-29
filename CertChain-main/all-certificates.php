<?php
include 'db-config.php';

$query = "
    SELECT c.id, c.certificate_id, c.title, c.issue_date, c.status, r.name AS recipient_name
    FROM certificates c
    JOIN recipients r ON c.recipient_id = r.id
    ORDER BY c.id DESC
";
$result = $conn->query($query);
$certificates = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $certificates[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Certificates</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f9ff;
            padding: 30px;
        }

        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 30px;
        }

        .search-bar {
            max-width: 400px;
            margin: 0 auto 20px auto;
            text-align: center;
        }

        .search-bar input {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid #007bff;
            border-radius: 8px;
            outline: none;
            font-size: 16px;
            transition: 0.3s;
        }

        .search-bar input:focus {
            border-color: #0056b3;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.4);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            animation: fadeInUp 0.5s ease;
        }

        th, td {
            padding: 12px 15px;
            text-align: center;
        }

        th {
            background: #007bff;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background: #f8f9fa;
        }

        .status-valid {
            color: green;
            font-weight: bold;
            animation: pulse 1.5s infinite alternate;
        }

        .status-pending {
            color: orange;
            font-weight: bold;
        }

        .btn-icon {
            margin: 0 5px;
            color: #007bff;
            text-decoration: none;
            transition: transform 0.2s ease;
        }

        .btn-icon:hover {
            color: #0056b3;
            transform: scale(1.1);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            from {
                transform: scale(1);
            }
            to {
                transform: scale(1.05);
            }
        }
    </style>
</head>
<body>

    <h2>All Certificates</h2>

    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Search by Certificate ID...">
    </div>

    <table id="certTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Certificate ID</th>
                <th>Title</th>
                <th>Recipient</th>
                <th>Issue Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($certificates)): ?>
                <?php foreach ($certificates as $certificate): ?>
                    <tr>
                        <td><?php echo $certificate['id']; ?></td>
                        <td><?php echo htmlspecialchars($certificate['certificate_id']); ?></td>
                        <td><?php echo htmlspecialchars($certificate['title']); ?></td>
                        <td><?php echo htmlspecialchars($certificate['recipient_name']); ?></td>
                        <td><?php echo $certificate['issue_date']; ?></td>
                        <td>
                            <span class="<?php echo $certificate['status'] === 'verified' ? 'status-valid' : 'status-pending'; ?>">
                                <?php echo ucfirst($certificate['status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="view-certificate.php?id=<?php echo $certificate['id']; ?>" class="btn-icon" title="View"><i class="fas fa-eye"></i></a>
                            <a href="edit-certificate.php?id=<?php echo $certificate['id']; ?>" class="btn-icon" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="download.php?id=<?php echo $certificate['id']; ?>" class="btn-icon" title="Download"><i class="fas fa-download"></i></a>
                            <a href="share.php?id=<?php echo $certificate['id']; ?>" class="btn-icon" title="Share"><i class="fas fa-share-alt"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7">No certificates found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
        const searchInput = document.getElementById("searchInput");
        searchInput.addEventListener("keyup", function () {
            const filter = searchInput.value.toUpperCase();
            const table = document.getElementById("certTable");
            const tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName("td")[1]; // certificate_id column
                if (td) {
                    const txtValue = td.textContent || td.innerText;
                    tr[i].style.display = txtValue.toUpperCase().includes(filter) ? "" : "none";
                }
            }
        });
    </script>

</body>
</html>
