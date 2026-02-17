<?php
include_once("./connection/connection.php");
$conn = connection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Directory kung saan ise-save ang images
    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = time() . '_' . basename($_FILES["profile_image"]["name"]);
    $targetFilePath = $targetDir . $fileName;

    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $allowedTypes = ["jpg", "jpeg", "png", "gif"];

    // Start HTML output
    echo "<!DOCTYPE html><html><head>
            <meta charset='UTF-8'>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          </head><body>";

    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetFilePath)) {

            // I-save sa database
            $stmt = $conn->prepare("INSERT INTO account (fullname, username, password, profile_image) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $fullname, $username, $password, $fileName);

            if ($stmt->execute()) {
                // SweetAlert2 popup with uploaded image
                echo "
                <script>
                    Swal.fire({
                        title: '✅ Account Created!',
                        text: 'Welcome, " . addslashes($fullname) . "!',
                        imageUrl: '$targetFilePath',
                        imageWidth: 150,
                        imageHeight: 150,
                        imageAlt: 'Profile Picture',
                        confirmButtonText: 'Go to Home'
                    }).then(() => {
                        window.location.href = 'index.php';
                    });
                </script>";
            } else {
                echo "<script>
                        Swal.fire('Database Error', '" . addslashes($stmt->error) . "', 'error');
                      </script>";
            }
            $stmt->close();
        } else {
            echo "<script>Swal.fire('Upload Failed', 'Could not move uploaded file.', 'error');</script>";
        }
    } else {
        echo "<script>Swal.fire('Invalid File', 'Only JPG, JPEG, PNG, and GIF files are allowed.', 'warning');</script>";
    }

    echo "</body></html>";
    $conn->close();
}
?>
