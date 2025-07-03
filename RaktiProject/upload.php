<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload File - Educlever</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .upload-container {
            max-width: 600px;
            margin: 50px auto;
            background: #f4f4f4;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .upload-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .upload-container form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .upload-container input[type="file"] {
            padding: 8px;
            background: #fff;
            border: 1px solid #ccc;
        }
        .upload-container button {
            padding: 10px;
            background: #007bff;
            border: none;
            color: white;
            cursor: pointer;
            font-weight: bold;
            border-radius: 5px;
        }
        .upload-container button:hover {
            background: #0056b3;
        }
        .file-info {
            margin-top: 10px;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
<nav class="navbar">
        <div class="logo">LearningCear</div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="contact.html">Contact</a></li>
            
                <li><a href="upload.php">Upload</a></li>
            
        </ul>
    </nav>

<div class="upload-container">
    <h2>Upload a File or Photo</h2>
    <form action="upload_handler.php" method="post" enctype="multipart/form-data">
        <input type="file" name="uploaded_file" required accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
        <div class="file-info">
            Allowed files: Images (JPG, JPEG, PNG, GIF), PDF, DOC, DOCX<br>
            Maximum file size: 5MB
        </div>
        <button type="submit">Upload</button>
    </form>
</div>

</body>
</html>
