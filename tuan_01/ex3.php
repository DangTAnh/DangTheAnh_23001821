<?php
function findBestStudent($students) {
    $best = $students[0];
    foreach ($students as $s) if ($s['score'] > $best['score']) $best = $s;
    return $best;
}

function findWorstStudent($students) {
    $worst = $students[0];
    foreach ($students as $s) if ($s['score'] < $worst['score']) $worst = $s;
    return $worst;
}

function countPassedStudents($students) {
    $count = 0;
    foreach ($students as $s) if ($s['score'] >= 5) $count++;
    return $count;
}

function findStudentByName($students, $name) {
    foreach ($students as $s) if ($s['name'] === $name) return $s;
    return null;
}

$students = [
    ["name" => "Nguyen Van An", "age" => 20, "score" => 8.5],
    ["name" => "Tran Thi Binh", "age" => 21, "score" => 6.5],
    ["name" => "Le Van Cuong", "age" => 19, "score" => 4.5],
    ["name" => "Pham Thi Dung", "age" => 20, "score" => 7.5],
];

$best = findBestStudent($students);
$worst = findWorstStudent($students);
echo "Cao nhất: {$best['name']} ({$best['score']})<br>";
echo "Thấp nhất: {$worst['name']} ({$worst['score']})<br>";
echo "Số SV đạt: " . countPassedStudents($students) . "<br>";

$found = findStudentByName($students, "Le Van Cuong");
echo $found ? "Tìm thấy: {$found['name']} ({$found['score']})" : "Không tìm thấy";
?>
<p><a href="./">← Tuần 1</a></p>
