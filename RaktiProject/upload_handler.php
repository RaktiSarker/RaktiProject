<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Please login to upload files");
}

$conn = new mysqli("localhost", "root", "", "user_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create uploads directory if it doesn't exist
$target_dir = "uploads/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// Get file information
$original_filename = basename($_FILES["uploaded_file"]["name"]);
$file_type = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
$filename = uniqid() . '_' . $original_filename;
$target_file = $target_dir . $filename;
$upload_ok = 1;

// Check file size (limit to 5MB)
if ($_FILES["uploaded_file"]["size"] > 5000000) {
    die("Sorry, your file is too large. Maximum size is 5MB.");
}

// Allow certain file formats
$allowed_types = array("jpg", "jpeg", "png", "gif", "pdf", "doc", "docx");
if (!in_array($file_type, $allowed_types)) {
    die("Sorry, only JPG, JPEG, PNG, GIF, PDF, DOC & DOCX files are allowed.");
}

if ($upload_ok == 0) {
    die("Sorry, your file was not uploaded.");
} else {
    if (move_uploaded_file($_FILES["uploaded_file"]["tmp_name"], $target_file)) {
        // Insert file information into database
        $stmt = $conn->prepare("INSERT INTO uploads (user_id, filename, original_filename, file_type, upload_date, file_path) VALUES (?, ?, ?, ?, NOW(), ?)");
        if ($stmt === false) {
            die('MySQL prepare error: ' . $conn->error);
        }
        
        $stmt->bind_param("issss", 
            $_SESSION['user_id'],
            $filename,
            $original_filename,
            $file_type,
            $target_file
        );

        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            // If database insert fails, delete the uploaded file
            unlink($target_file);
            die("Error: " . $stmt->error);
        }
        $stmt->close();
    } else {
        die("Sorry, there was an error uploading your file.");
    }
}

$conn->close();
?>
