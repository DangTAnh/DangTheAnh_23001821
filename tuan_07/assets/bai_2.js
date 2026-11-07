const catalogApp = Vue.createApp({
  data() {
    return {
      products: [
        { id: 1, name: 'Bút bi', price: 5000, image: 'assets/images/but.svg', description: 'Bút bi mực xanh, ngòi 0.5 mm.', stock: 20 },
        { id: 2, name: 'Vở ô ly', price: 12000, image: 'assets/images/vo.svg', description: 'Vở ô ly 96 trang, khổ A5.', stock: 15 },
        { id: 3, name: 'Chuột máy tính', price: 150000, image: 'assets/images/chuot.svg', description: 'Chuột quang có dây, kết nối USB.', stock: 5 },
        { id: 4, name: 'Bình nước', price: 80000, image: 'assets/images/binh.svg', description: 'Bình đựng nước dung tích 500 ml.', stock: 0 },
      ],
      notice: '',
    };
  },
  methods: {
    showPurchaseNotice(name) {
      this.notice = 'Bạn đã chọn mua: ' + name + '.';
    },
  },
});

catalogApp.component('the-san-pham', TheSanPham);
catalogApp.mount('#app');
