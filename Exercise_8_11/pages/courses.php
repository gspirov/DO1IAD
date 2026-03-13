<?php

use Entity\Course;
use PDO\Connection;

$stmt = Connection::getPdo()->prepare('SELECT id, course_id AS courseId, `name` FROM `course`');
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_CLASS, Course::class);

?>

<table>
	<thead>
		<tr>
			<th>Course ID</th>
			<th>Name</th>
			<th>Actions</th>
		</tr>
	</thead>
	<?php if (!empty($courses)): ?>
		<?php
        /* @var Course $course */
        foreach ($courses as $course): ?>
			<tr>
				<td><?= htmlspecialchars($course->courseId); ?></td>
				<td><?= htmlspecialchars($course->name); ?></td>
				<td>
					<a href="<?= sprintf('/course/%s', $course->id); ?>">View</a>
				</td>
			</tr>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="2">No courses found</td>
		</tr>
	<?php endif; ?>
</table>
