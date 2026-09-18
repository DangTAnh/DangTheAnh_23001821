<?php
$students = [
    ["name" => "Nguyen Van An", "age" => 20, "score" => 8.5],
    ["name" => "Tran Thi Binh", "age" => 21, "score" => 6.5],
    ["name" => "Le Van Cuong", "age" => 19, "score" => 4.5],
    ["name" => "Pham Thi Dung", "age" => 20, "score" => 7.5],
];

$total = 0;
foreach ($students as $s) {
    echo "Họ tên: {$s['name']} - Tuổi: {$s['age']} - Điểm: {$s['score']}<br>";
    $total += $s['score'];
}
echo "Điểm trung bình: " . ($total / count($students));
?>
<p><a href="./">← Tuần 1</a></p>
