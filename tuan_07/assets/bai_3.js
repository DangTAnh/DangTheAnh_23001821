const cartApp = Vue.createApp({
  data() {
    return {
      cart: [
        { id: 1, name: 'Bút bi', price: 5000, quantity: 2 },
        { id: 2, name: 'Vở ô ly', price: 12000, quantity: 3 },
      ],
      error: '',
    };
  },
  computed: {
    totalQuantity() {
      return this.cart.reduce((total, item) => total + item.quantity, 0);
    },
    totalPrice() {
      return this.cart.reduce((total, item) => total + item.price * item.quantity, 0);
    },
  },
  methods: {
    money(value) {
      return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
    },
    changeQuantity(id, quantity) {
      this.error = '';
      const item = this.cart.find(item => item.id === id);
      if (!item) {
        this.error = 'Không tìm thấy sản phẩm.';
        return;
      }
      if (!Number.isInteger(quantity) || quantity < 1 || quantity > 99) {
        this.error = 'Số lượng phải là số nguyên từ 1 đến 99.';
        return;
      }
      item.quantity = quantity;
    },
    removeItem(id) {
      this.error = '';
      if (!this.cart.some(item => item.id === id)) {
        this.error = 'Không tìm thấy sản phẩm.';
        return;
      }
      this.cart = this.cart.filter(item => item.id !== id);
    },
  },
});

cartApp.component('dong-gio-hang', DongGioHang);
cartApp.mount('#app');
