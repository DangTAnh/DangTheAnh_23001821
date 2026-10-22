<?php
function getRank($score) {
    if (!is_numeric($score)) {
        throw new InvalidArgumentException("Điểm phải là một số.");
    }
    if ($score < 0 || $score > 10) {
        throw new InvalidArgumentException("Điểm phải nằm trong khoảng từ 0 đến 10.");
    }
    
    if ($score >= 8) {
        return "Giỏi";
    } else if ($score >= 6.5) {
        return "Khá";
    } else if ($score >= 5) {
        return "Trung bình";
    } else {
        return "Yếu";
    }
    return null;
}

$name = "Nguyen Van An";
$score = 8.5;

try {
    $rank = getRank($score);
} catch (InvalidArgumentException $e) {
    echo "Lỗi: " . $e->getMessage();
    exit;
}

if ($rank !== null) {
    echo "Họ tên: $name - Điểm: $score - Xếp loại: $rank<br>";
} else {
    echo "Không thể xác định xếp loại cho điểm: $score<br>";
}

?>

<p><a href="./">← Trở lại</a></p>
