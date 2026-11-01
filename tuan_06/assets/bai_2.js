// Bài 2: v-model với modifier, methods, computed và @submit.prevent.
Vue.createApp({
  data() {
    return {
      movie: 'Doraemon',
      customerName: '',
      showtimes: ['09:00', '14:00', '19:30'],
      showtime: '09:00',
      ticketType: 'standard',
      quantity: 1,
      hasCombo: false,
      confirmation: null,
    };
  },
  computed: {
    ticketPrice() {
      return this.ticketType === 'vip' ? 100000 : 70000;
    },
    validQuantity() {
      return Number.isInteger(this.quantity) && this.quantity >= 1 && this.quantity <= 10;
    },
    totalPrice() {
      return this.validQuantity ? this.ticketPrice * this.quantity + (this.hasCombo ? 45000 : 0) : 0;
    },
    canBook() {
      return this.customerName.trim().length > 0 && this.validQuantity;
    },
  },
  methods: {
    money(value) {
      return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
    },
    changeQuantity(change) {
      if (!this.validQuantity) return;
      this.quantity = Math.min(10, Math.max(1, this.quantity + change));
    },
    resetForm() {
      this.customerName = '';
      this.showtime = this.showtimes[0];
      this.ticketType = 'standard';
      this.quantity = 1;
      this.hasCombo = false;
      this.confirmation = null;
    },
    bookTickets() {
      if (!this.canBook) return;
      // Lưu một bản xác nhận độc lập với dữ liệu đang chỉnh sửa trong form.
      this.confirmation = {
        customerName: this.customerName.trim(),
        movie: this.movie,
        showtime: this.showtime,
        quantity: this.quantity,
        ticketLabel: this.ticketType === 'vip' ? 'Vé VIP' : 'Vé thường',
        hasCombo: this.hasCombo,
        totalPrice: this.totalPrice,
      };
    },
  },
}).mount('#app');
