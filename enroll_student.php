<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $grade = $_POST['grade'];
    $semester = $_POST['semester'];

    $sql = "INSERT INTO Enrollment (student_id, course_id, grade, semester) VALUES (:student_id, :course_id, :grade, :semester)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'student_id' => $student_id,
        'course_id' => $course_id,
        'grade' => $grade,
        'semester' => $semester
    ]);

    header("Location: student_detail.php?id=$student_id");
    exit;
}

// Fetch students and courses for the dropdown options
$students = $pdo->query("SELECT * FROM Student")->fetchAll(PDO::FETCH_ASSOC);
$courses = $pdo->query("SELECT * FROM Course")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Enroll Student</h2>
    <form action="enroll_student.php" method="POST">
        <select name="student_id" required>
            <option value="">Select Student</option>
            <?php foreach ($students as $student): ?>
                <option value="<?= $student['student_id'] ?>"><?= $student['name'] ?></option>
            <?php endforeach; ?>
        </select>

        <select name="course_id" required>
            <option value="">Select Course</option>
            <?php foreach ($courses as $course): ?>
                <option value="<?= $course['course_id'] ?>"><?= $course['course_name'] ?></option>
            <?php endforeach; ?>
        </select>

        <input type="text" name="grade" placeholder="Grade (A/B/C/etc.)" required>
        <input type="text" name="semester" placeholder="Semester" required>
        <button type="submit">Enroll</button>
    </form>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
