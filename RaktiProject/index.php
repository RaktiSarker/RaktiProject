<?php
session_start();
$conn = new mysqli("localhost", "root", "", "user_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduClever - Education Theme</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="courses.css">
    <style>
        .uploads-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }
        .upload-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .upload-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
        }
        .upload-card .file-info {
            margin-top: 10px;
        }
        .upload-card .file-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .upload-card .upload-date {
            color: #666;
            font-size: 0.9em;
        }
        .upload-card .student-info {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            font-size: 0.9em;
        }
        .pdf-preview {
            background: #f5f5f5;
            padding: 20px;
            text-align: center;
            border-radius: 4px;
        }
        .download-link {
            display: inline-block;
            padding: 8px 15px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 10px;
        }
        .download-link:hover {
            background: #0056b3;
        }
        .welcome-text {
            color: #333;
            font-size: 1.1em;
            margin-right: 10px;
        }
    </style>
</head>
<body>

    <header class="top-bar">
        <div class="auth-links">
            <?php if (isset($_SESSION['user_id'])): 
                $stmt = $conn->prepare("SELECT username, studentID, section, semester, department FROM users WHERE id = ?");
                $stmt->bind_param("i", $_SESSION['user_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
            ?>
                <span class="welcome-text">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</span> |
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a> | <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </header>

    <nav class="navbar">
        <div class="logo">LearningCear</div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="contact.html">Contact</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="upload.php">Upload</a></li>
            <?php endif; ?>
        </ul>
        
    </nav>

    <section class="hero" style="background-image: url(e-commerce.png); background-size: cover; background-position: center;">
        <div class="overlay"></div>
        <div class="hero-content">
            <h2>Education is the <strong>key</strong> to Success</h2>
            <p>Making an Impact in Classrooms and Communities</p>
           
        </div>
    </section>

    <section class="info-section">
        <h2>Academic Residential College Made up of Students</h2>
    </section>

    <?php if (isset($_SESSION['user_id'])): ?>
        <section class="info-section">
            <h2>Uploaded Files</h2>
            <div class="uploads-grid">
                <?php
                $query = "SELECT u.*, usr.username, usr.studentID, usr.section, usr.semester, usr.department 
                         FROM uploads u 
                         JOIN users usr ON u.user_id = usr.id 
                         ORDER BY u.upload_date DESC";
                $result = $conn->query($query);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $ext = strtolower(pathinfo($row['filename'], PATHINFO_EXTENSION));
                        ?>
                        <div class="upload-card">
                            <?php if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                <img src="<?php echo htmlspecialchars($row['file_path']); ?>" alt="<?php echo htmlspecialchars($row['original_filename']); ?>">
                            <?php elseif ($ext == 'pdf'): ?>
                                <div class="pdf-preview">
                                    <svg width="50" height="50" viewBox="0 0 24 24">
                                        <path fill="currentColor" d="M19 3H5C3.9 3 3 3.9 3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-9.5 8.5c0 .83-.67 1.5-1.5 1.5H7v2H5.5V9H8c.83 0 1.5.67 1.5 1.5v1zm10 2.5h-1.5v2H16v-2h-1.5V9H16v2h1.5V9H19v5zm-5 0h-2V9h2c.83 0 1.5.67 1.5 1.5v2c0 .83-.67 1.5-1.5 1.5z"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                            <div class="file-info">
                                <div class="file-name"><?php echo htmlspecialchars($row['original_filename']); ?></div>
                                <div class="upload-date">
                                    Uploaded: <?php echo date('M d, Y H:i', strtotime($row['upload_date'])); ?>
                                </div>
                                <div class="student-info">
                                    <strong>Uploaded by:</strong> <?php echo htmlspecialchars($row['username']); ?><br>
                                    <strong>Student ID:</strong> <?php echo htmlspecialchars($row['studentID']); ?><br>
                                    <strong>Section:</strong> <?php echo htmlspecialchars($row['section']); ?><br>
                                    <strong>Semester:</strong> <?php echo htmlspecialchars($row['semester']); ?><br>
                                    <strong>Department:</strong> <?php echo htmlspecialchars($row['department']); ?>
                                </div>
                                <a href="<?php echo htmlspecialchars($row['file_path']); ?>" class="download-link" target="_blank">
                                    <?php echo in_array($ext, ['jpg', 'jpeg', 'png', 'gif']) ? 'View Full Size' : 'Download File'; ?>
                                </a>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p>No files have been uploaded yet.</p>";
                }
                ?>
            </div>
        </section>
    <?php endif; ?>

</body>
</html>
