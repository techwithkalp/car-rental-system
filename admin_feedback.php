<?php
session_start();
include '../config.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch all feedback with user details
$feedbacks = $conn->query("
    SELECT f.*, u.username, u.email 
    FROM feedback f
    JOIN users u ON f.user_id = u.id
    ORDER BY f.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --background-color: #f4f7f9;
            --card-bg: #ffffff;
            --shadow-color: rgba(0, 0, 0, 0.1);
            --title-color: #2c3e50;
            --accent-color: #f39c12;
            --table-header-bg: #2c3e50;
            --table-text-color: #ecf0f1;
            --table-row-hover: #f8f9fa;
        }

        body {
            background-color: var(--background-color);
            color: var(--title-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            flex: 1;
            margin-top: 3rem;
            margin-bottom: 3rem;
        }

        h2 {
            font-weight: 700;
            color: var(--title-color);
            margin-bottom: 2rem;
            text-align: center;
        }

        .feedback-container {
            background-color: var(--card-bg);
            border-radius: 15px;
            box-shadow: 0 10px 30px var(--shadow-color);
            padding: 2rem;
            animation: fadeIn 0.8s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .table-custom {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 12px;
            overflow: hidden;
        }

        .table-custom thead th {
            background-color: var(--table-header-bg);
            color: var(--table-text-color);
            border-bottom: 3px solid var(--accent-color);
            font-weight: 600;
            vertical-align: middle;
        }

        .table-custom tbody tr td {
            vertical-align: middle;
            font-size: 0.95rem;
        }

        .table-custom tbody tr:hover {
            background-color: var(--table-row-hover);
        }

        .message-cell {
            white-space: pre-wrap;
            word-wrap: break-word;
            max-width: 300px;
        }
        
        .no-data {
            color: #7f8c8d;
        }

    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <h2>User Feedbacks</h2>
        <div class="feedback-container">
            <div class="table-responsive">
                <table class="table table-custom table-hover table-striped align-middle">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">User</th>
                            <th scope="col">Email</th>
                            <th scope="col">Subject</th>
                            <th scope="col">Message</th>
                            <th scope="col">Submitted At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($feedbacks->num_rows > 0): ?>
                            <?php while ($row = $feedbacks->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['username']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['subject']) ?></td>
                                    <td class="message-cell"><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                                    <td><?= date("M d, Y H:i", strtotime($row['created_at'])) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center no-data py-4">
                                    <i class="bi bi-chat-dots d-block mb-2" style="font-size: 2rem;"></i>
                                    No feedback has been submitted yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>