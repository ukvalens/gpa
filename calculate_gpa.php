<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Check if the user has the appropriate role if needed




include 'db.php';

$error = '';
$gpa = 0;
$student = null;
$enrollments = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['student_id'])) {
    $student_id = filter_var($_POST['student_id'], FILTER_VALIDATE_INT);
    
    if ($student_id) {
        try {
            // Fetch student details
            $stmt = $pdo->prepare("SELECT * FROM Student WHERE student_id = :student_id");
            $stmt->execute(['student_id' => $student_id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if the student exists
            if ($student) {
                // Fetch student enrollments
                $stmt = $pdo->prepare("SELECT e.*, c.course_name, c.credit_hours 
                                       FROM Enrollment e 
                                       JOIN Course c ON e.course_id = c.course_id 
                                       WHERE e.student_id = :student_id");
                $stmt->execute(['student_id' => $student_id]);
                $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // GPA Calculation
                $total_points = 0;
                $total_credits = 0;

                $grade_point_map = ['A' => 4.0, 'B' => 3.0, 'C' => 2.0, 'D' => 1.0, 'F' => 0.0];

                foreach ($enrollments as $enrollment) {
                    $grade_point = $grade_point_map[$enrollment['grade']] ?? 0;
                    $credit_hours = $enrollment['credit_hours'];
                    $total_points += $grade_point * $credit_hours;
                    $total_credits += $credit_hours;
                }

                $gpa = $total_credits ? round($total_points / $total_credits, 2) : 0;
            } else {
                $error = "Student not found!";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    } else {
        $error = "Invalid student ID!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GPA Calculation</title>
</head>
<body>
    <h1>Calculate GPA</h1>
    <form action="calculate_gpa.php" method="post">
        <label for="student_id">Student ID:</label>
        <input type="number" id="student_id" name="student_id" required>
        <button type="submit">Calculate GPA</button>
    </form>

    <?php if ($error): ?>
        <p><?= htmlspecialchars($error) ?></p>
    <?php elseif ($student): ?>
        <h2><?= htmlspecialchars($student['name']) ?>'s GPA</h2>
        <p>Department: <?= htmlspecialchars($student['department']) ?></p>
        <p>Level: <?= htmlspecialchars($student['level']) ?></p>
        <p>GPA: <?= $gpa ?></p>

        <h3>Enrolled Courses</h3>
        <ul>
            <?php foreach ($enrollments as $enrollment): ?>
                <li>
                    <?= htmlspecialchars($enrollment['course_name']) ?> - 
                    Grade: <?= htmlspecialchars($enrollment['grade']) ?> - 
                    Credit Hours: <?= htmlspecialchars($enrollment['credit_hours']) ?> - 
                    Semester: <?= htmlspecialchars($enrollment['semester']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
        
    <?php endif; ?>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
