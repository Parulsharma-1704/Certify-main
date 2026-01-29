<?php
require 'vendor/autoload.php'; // or require the autoload file from dompdf if downloaded manually
use Dompdf\Dompdf;

include 'db-config.php';

if (!isset($_GET['id'])) {
    die("Certificate ID not provided.");
}

$id = intval($_GET['id']);
$query = "
    SELECT c.*, r.name AS recipient_name 
    FROM certificates c 
    JOIN recipients r ON c.recipient_id = r.id 
    WHERE c.id = $id
";
$result = $conn->query($query);

if ($result->num_rows === 1) {
    $certificate = $result->fetch_assoc();

    // Create HTML structure for the PDF
    $html = '
        <style>
            body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
            .certificate-box {
                border: 10px solid #007bff;
                padding: 40px;
                border-radius: 20px;
            }
            h1 { color: #007bff; }
            .info { font-size: 18px; margin-top: 20px; }
        </style>

        <div class="certificate-box">
            <h1>Certificate of Achievement</h1>
            <p>This is to certify that</p>
            <h2>' . htmlspecialchars($certificate['recipient_name']) . '</h2>
            <p class="info">has successfully completed the course titled:</p>
            <h3>' . htmlspecialchars($certificate['title']) . '</h3>
            <p class="info">Issued on: ' . htmlspecialchars($certificate['issue_date']) . '</p>
            <p class="info">Certificate ID: ' . htmlspecialchars($certificate['certificate_id']) . '</p>
        </div>
    ';

    // Initialize Dompdf
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);

    // (Optional) Set paper size and orientation
    $dompdf->setPaper('A4', 'landscape');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF (force download)
    $dompdf->stream("certificate_" . $certificate['certificate_id'] . ".pdf", ["Attachment" => true]);

    exit;
} else {
    die("Certificate not found.");
}
?>
