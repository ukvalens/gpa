<?php
include 'db.php';

// Initialize error variable
$error = "";

// Check if 'id' is set in the URL and not empty
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $student_id = $_GET['id'];

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
} else {
    $error = "No student selected!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($student) ? htmlspecialchars($student['name']) . "'s GPA" : "Error" ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1, p {
            margin: 10px 0;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin-bottom: 5px;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php else: ?>
        <h1><?= htmlspecialchars($student['name']) ?>'s GPA</h1>
        <p>Department: <?= htmlspecialchars($student['department']) ?></p>
        <p>Level: <?= htmlspecialchars($student['level']) ?></p>
        <p>GPA: <?= htmlspecialchars($gpa) ?></p>

        <h2>Enrolled Courses</h2>
        <?php if (!empty($enrollments)): ?>
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
        <?php else: ?>
            <p>No courses found for this student.</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
