<?php
class CartItem {
    public $name;
    public $price;
    public $quantity;

    public function __construct($name, $price, $quantity) {
        if (!is_string($name) || trim($name) === '') {
            throw new InvalidArgumentException("Tên sản phẩm không được rỗng.");
        }
        if (!is_numeric($price) || $price <= 0) {
            throw new InvalidArgumentException("Giá của '$name' phải > 0.");
        }
        if (!is_numeric($quantity) || (int)$quantity != $quantity || $quantity <= 0) {
            throw new InvalidArgumentException("Số lượng của '$name' phải là số nguyên > 0.");
        }
        $this->name = trim($name);
        $this->price = $price + 0;
        $this->quantity = (int)$quantity;
    }

    public function getTotal() {
        return $this->price * $this->quantity;
    }
}

class ShoppingCart {
    private $items = [];

    public function addItem(CartItem $item) {
        $this->items[] = $item;
        echo "Đã thêm: {$item->name} x{$item->quantity}<br>";
    }

    public function removeItem($name) {
        foreach ($this->items as $i => $item) {
            if (strcasecmp($item->name, trim($name)) === 0) {
                unset($this->items[$i]);
                $this->items = array_values($this->items);
                echo "Đã xóa: $name<br>";
                return true;
            }
        }
        echo "Không tìm thấy sản phẩm: $name<br>";
        return false;
    }

    public function calculateTotal() {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->getTotal();
        }
        return $total;
    }

    public function displayCart() {
        if (empty($this->items)) {
            echo "Giỏ hàng trống.<br>";
            return;
        }
        foreach ($this->items as $item) {
            echo "Tên: {$item->name} - Giá: {$item->price} - SL: {$item->quantity} - Thành tiền: {$item->getTotal()}<br>";
        }
        echo "Tổng tiền: " . $this->calculateTotal() . "<br>";
    }
}

function safeAdd($cart, $name, $price, $quantity) {
    try {
        $cart->addItem(new CartItem($name, $price, $quantity));
    } catch (InvalidArgumentException $e) {
        echo "Thêm thất bại ($name): " . $e->getMessage() . "<br>";
    }
}


$cart = new ShoppingCart();
safeAdd($cart, "Áo thun", 150000, 2);
safeAdd($cart, "Quần jeans", 350000, 1);
safeAdd($cart, "Giày sneaker", 800000, 1);
safeAdd($cart, "Mũ lưỡi trai", 120000, 3);
safeAdd($cart, "Hàng lỗi giá", 0, 1);
safeAdd($cart, "Hàng lỗi SL", 50000, -2);

echo "<b>Giỏ hàng:</b><br>";
$cart->displayCart();

$cart->removeItem("Quần jeans");
$cart->removeItem("Không tồn tại");

echo "<b>Giỏ hàng sau khi xóa:</b><br>";
$cart->displayCart();

$empty = new ShoppingCart();
echo "Tổng giỏ trống: " . $empty->calculateTotal() . "<br>";
$empty->displayCart();
?>
<p><a href="./">← Tuần 2</a></p>
