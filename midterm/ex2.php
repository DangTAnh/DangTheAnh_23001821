<?php

$sanpham = [
    ["name" => "Bút chì", "price" => 100],
    ["name" => "Thước kẻ", "price" => 200],
    ["name" => "Tẩy", "price" => 150],
    ["name" => "Bút bi", "price" => 250],
    ["name" => "Sách", "price" => 175]
];

function calculateTotal($items) {
    if (!is_array($items)) {
        throw new InvalidArgumentException("Dữ liệu không hợp lệ: phải là mảng.");
    }
    if (empty($items)) {
        return 0;
    }

    $total = array_sum(array_column($items, 'price'));

    return $total;
}

function findMaxPrice($items) {
    if (!is_array($items)) {
        throw new InvalidArgumentException("Dữ liệu không hợp lệ: phải là mảng.");
    }
    if (empty($items)) {
        return null;
    }

    $maxPrice = $items[0]['price'];
    foreach ($items as $item) {
        if ($item['price'] > $maxPrice) {
            $maxPrice = $item['price'];
        }
    }

    return $maxPrice;
}


try {
    $total = calculateTotal($sanpham);
    $maxPrice = findMaxPrice($sanpham);
} catch (InvalidArgumentException $e) {
    echo "Lỗi: " . $e->getMessage();
    exit;
}

echo "Tổng giá trị giỏ hàng: $total<br>" . "Giá trị cao nhất: $maxPrice<br>";

?>

<p><a href="./">← Trở lại</a></p>

