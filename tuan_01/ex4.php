<?php
class Student {
    public $name;
    public $age;
    public $score;

    public function __construct($name, $age, $score) {
        $this->name = $name;
        $this->age = $age;
        $this->score = $score;
    }

    public function getRank() {
        if ($this->score >= 8) return "Giỏi";
        if ($this->score >= 6.5) return "Khá";
        if ($this->score >= 5) return "Trung bình";
        return "Yếu";
    }

    public function isPassed() {
        return $this->score >= 5;
    }

    public function display() {
        echo "Họ tên: {$this->name} - Tuổi: {$this->age} - Điểm: {$this->score} - Xếp loại: {$this->getRank()}<br>";
    }
}

function averageScore($students) {
    $total = 0;
    foreach ($students as $s) $total += $s->score;
    return $total / count($students);
}

function bestStudent($students) {
    $best = $students[0];
    foreach ($students as $s) if ($s->score > $best->score) $best = $s;
    return $best;
}

function countPassed($students) {
    $count = 0;
    foreach ($students as $s) if ($s->isPassed()) $count++;
    return $count;
}

$students = [
    new Student("Nguyen Van An", 20, 8.5),
    new Student("Tran Thi Binh", 21, 6.5),
    new Student("Le Van Cuong", 19, 4.5),
    new Student("Pham Thi Dung", 20, 7.5),
];

foreach ($students as $s) $s->display();
echo "Cao nhất: " . bestStudent($students)->name . "<br>";
echo "Số SV đạt: " . countPassed($students) . "<br>";
echo "Điểm trung bình: " . averageScore($students);
?>
<p><a href="./">← Tuần 1</a></p>
