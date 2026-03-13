<?php

/* @var string $courseId */

use Entity\Course;
use PDO\Connection;

$stmt = Connection::getPdo()->prepare('SELECT id, course_id AS courseId, `name` FROM `course` WHERE id = ?');
$stmt->execute([$courseId]);
$course = $stmt->fetchObject(Course::class);

if (empty($course)) {
    http_response_code(404);
    echo '<p>Course not found.</p>';
} else { ?>
    <p>Course ID: <?= htmlspecialchars($course->id); ?></p>
    <p>Name: <?= htmlspecialchars($course->name); ?></p>
<?php }

?>


