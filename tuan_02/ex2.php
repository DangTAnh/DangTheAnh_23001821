<?php
class Movie {
    public $id;
    public $title;
    public $price;
    public $totalSeats;
    public $availableSeats;

    public function __construct($id, $title, $price, $totalSeats) {
        if (!is_numeric($id) || $id <= 0) {
            throw new InvalidArgumentException("ID phim phải > 0.");
        }
        if (!is_string($title) || trim($title) === '') {
            throw new InvalidArgumentException("Tên phim không được rỗng.");
        }
        if (!is_numeric($price) || $price <= 0) {
            throw new InvalidArgumentException("Giá vé phải > 0.");
        }
        if (!is_numeric($totalSeats) || (int)$totalSeats != $totalSeats || $totalSeats <= 0) {
            throw new InvalidArgumentException("Tổng số ghế phải là số nguyên > 0.");
        }
        $this->id = (int)$id;
        $this->title = trim($title);
        $this->price = $price + 0;
        $this->totalSeats = (int)$totalSeats;
        $this->availableSeats = $this->totalSeats;
    }

    public function bookTicket($quantity) {
        if (!is_numeric($quantity) || (int)$quantity != $quantity || $quantity <= 0) {
            throw new InvalidArgumentException("Số vé đặt phải là số nguyên > 0.");
        }
        if ($quantity > $this->availableSeats) {
            throw new InvalidArgumentException("Chỉ còn {$this->availableSeats} ghế, không đặt được $quantity vé.");
        }
        $this->availableSeats -= (int)$quantity;
    }

    public function cancelTicket($quantity) {
        if (!is_numeric($quantity) || (int)$quantity != $quantity || $quantity <= 0) {
            throw new InvalidArgumentException("Số vé hủy phải là số nguyên > 0.");
        }
        if ($quantity > $this->getSoldSeats()) {
            throw new InvalidArgumentException("Mới bán {$this->getSoldSeats()} vé, không hủy được $quantity vé.");
        }
        $this->availableSeats += (int)$quantity;
    }

    public function getSoldSeats() {
        return $this->totalSeats - $this->availableSeats;
    }

    public function getRevenue() {
        return $this->getSoldSeats() * $this->price;
    }

    public function displayInfo() {
        echo "ID: {$this->id} - Phim: {$this->title} - Giá: {$this->price} - "
            . "Tổng: {$this->totalSeats} - Còn: {$this->availableSeats} - "
            . "Đã bán: {$this->getSoldSeats()} - Doanh thu: {$this->getRevenue()}<br>";
    }
}

function findMovieById($movies, $id) {
    if (empty($movies)) return null;
    foreach ($movies as $m) {
        if ($m->id == $id) return $m;
    }
    return null;
}

function getTotalRevenue($movies) {
    if (empty($movies)) return 0;
    $total = 0;
    foreach ($movies as $m) $total += $m->getRevenue();
    return $total;
}

function getBestSellingMovie($movies) {
    if (empty($movies)) return null;
    $best = $movies[0];
    foreach ($movies as $m) {
        if ($m->getSoldSeats() > $best->getSoldSeats()) $best = $m;
    }
    return $best;
}

function tryBook($movie, $quantity) {
    try {
        $movie->bookTicket($quantity);
        echo "Đặt $quantity vé '{$movie->title}': OK<br>";
    } catch (InvalidArgumentException $e) {
        echo "Đặt $quantity vé '{$movie->title}': " . $e->getMessage() . "<br>";
    }
}

function tryCancel($movie, $quantity) {
    try {
        $movie->cancelTicket($quantity);
        echo "Hủy $quantity vé '{$movie->title}': OK<br>";
    } catch (InvalidArgumentException $e) {
        echo "Hủy $quantity vé '{$movie->title}': " . $e->getMessage() . "<br>";
    }
}

// --- Chương trình chính ---
try {
    $movies = [
        new Movie(1, "Avengers", 100000, 100),
        new Movie(2, "Avatar", 120000, 80),
        new Movie(3, "Batman", 90000, 120),
    ];
} catch (InvalidArgumentException $e) {
    die("Dữ liệu phim lỗi: " . $e->getMessage());
}

$avengers = findMovieById($movies, 1);
$avatar = findMovieById($movies, 2);

tryBook($avengers, 10);
tryBook($avatar, 5);
tryCancel($avengers, 3);

echo "<b>Thông tin các phim:</b><br>";
foreach ($movies as $m) $m->displayInfo();

echo "Tổng doanh thu: " . getTotalRevenue($movies) . "<br>";
$best = getBestSellingMovie($movies);
echo $best ? "Bán chạy nhất: {$best->title} ({$best->getSoldSeats()} vé)<br>" : "Chưa có phim nào.<br>";

echo "<b>Kiểm tra ngoại lệ:</b><br>";
tryBook($avengers, 0);
tryBook($avengers, 999);
tryCancel($avengers, 0);
tryCancel($avengers, 999);  

$ghost = findMovieById($movies, 99);
echo $ghost ? $ghost->displayInfo() : "Không tìm thấy phim ID 99<br>";

$empty = [];
echo "Doanh thu danh sách rỗng: " . getTotalRevenue($empty) . "<br>";
echo getBestSellingMovie($empty) ? "Lỗi: phải null" : "Best-seller rỗng: null (đúng)<br>";
echo findMovieById($empty, 1) ? "Lỗi: phải null" : "Tìm trong rỗng: null (đúng)<br>";
?>
<p><a href="./">← Tuần 2</a></p>
