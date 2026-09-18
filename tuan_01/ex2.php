<?php
function calculateAverageScore($students) {
    $total = 0;
    foreach ($students as $s) $total += $s['score'];
    return $total / count($students);
}

function getRank($score) {
    if ($score >= 8) return "Giỏi";
    if ($score >= 6.5) return "Khá";
    if ($score >= 5) return "Trung bình";
    return "Yếu";
}

function displayStudent($student) {
    echo "Họ tên: {$student['name']} - Tuổi: {$student['age']} - Điểm: {$student['score']} - Xếp loại: " . getRank($student['score']) . "<br>";
}

$students = [
    ["name" => "Nguyen Van An", "age" => 20, "score" => 8.5],
    ["name" => "Tran Thi Binh", "age" => 21, "score" => 6.5],
    ["name" => "Le Van Cuong", "age" => 19, "score" => 4.5],
    ["name" => "Pham Thi Dung", "age" => 20, "score" => 7.5],
];

foreach ($students as $s) displayStudent($s);
echo "Điểm trung bình: " . calculateAverageScore($students);
?>
<p><a href="./">← Tuần 1</a></p>
