Vue.createApp({
  data() {
    return {
      products: [
        { id: 1, name: 'Bút bi', price: 5000, category: 'Văn phòng phẩm', stock: 20 },
        { id: 2, name: 'Vở ô ly', price: 12000, category: 'Văn phòng phẩm', stock: 15 },
        { id: 3, name: 'Chuột máy tính', price: 150000, category: 'Phụ kiện máy tính', stock: 5 },
        { id: 4, name: 'Bàn phím', price: 350000, category: 'Phụ kiện máy tính', stock: 0 },
        { id: 5, name: 'Bình nước', price: 80000, category: 'Đồ gia dụng', stock: 8 },
        { id: 6, name: 'Đèn bàn', price: 220000, category: 'Đồ gia dụng', stock: 3 },
      ],
      categories: ['Văn phòng phẩm', 'Phụ kiện máy tính', 'Đồ gia dụng'],
      nextId: 7,
      keyword: '',
      selectedCategory: '',
      sortOrder: '',
      keywordMessage: '',
      pageStatus: 'Đang tải',
      newProduct: { name: '', price: '', category: 'Văn phòng phẩm', stock: 0 },
      formError: '',
      notice: '',
    };
  },
  computed: {
    filteredProducts() {
      const keyword = this.keyword.toLocaleLowerCase('vi-VN');
      // filter tạo mảng mới nên sort không đổi thứ tự mảng products gốc.
      const result = this.products.filter(product => {
        return product.name.toLocaleLowerCase('vi-VN').includes(keyword)
          && (!this.selectedCategory || product.category === this.selectedCategory);
      });
      if (this.sortOrder === 'asc') result.sort((a, b) => a.price - b.price);
      if (this.sortOrder === 'desc') result.sort((a, b) => b.price - a.price);
      return result;
    },
    visibleCount() {
      return this.filteredProducts.length;
    },
    inventoryValue() {
      return this.filteredProducts.reduce((total, product) => total + product.price * product.stock, 0);
    },
  },
  watch: {
    keyword(value) {
      this.keywordMessage = Array.from(value).length === 1
        ? 'Từ khóa chỉ có 1 ký tự. Nhập thêm để tìm chính xác hơn.'
        : '';
    },
  },
  mounted() {
    this.pageStatus = 'Sẵn sàng';
  },
  methods: {
    money(value) {
      return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
    },
    addProduct() {
      this.formError = '';
      this.notice = '';
      const name = this.newProduct.name.trim();
      const price = this.newProduct.price;
      const stock = this.newProduct.stock;
      if (!name) {
        this.formError = 'Tên sản phẩm không được để trống.';
        return;
      }
      if (typeof price !== 'number' || !Number.isFinite(price) || price <= 0) {
        this.formError = 'Giá sản phẩm phải lớn hơn 0.';
        return;
      }
      if (!Number.isInteger(stock) || stock < 0) {
        this.formError = 'Tồn kho phải là số nguyên không âm.';
        return;
      }
      if (!this.categories.includes(this.newProduct.category)) {
        this.formError = 'Danh mục không hợp lệ.';
        return;
      }
      this.products.push({ id: this.nextId++, name, price, category: this.newProduct.category, stock });
      this.newProduct = { name: '', price: '', category: this.categories[0], stock: 0 };
      this.notice = 'Đã thêm sản phẩm ' + name + '. Sản phẩm sẽ hiển thị nếu khớp bộ lọc hiện tại.';
    },
    deleteProduct(id) {
      const product = this.products.find(product => product.id === id);
      if (!product || !window.confirm('Bạn có muốn xóa sản phẩm "' + product.name + '"?')) return;
      this.products = this.products.filter(product => product.id !== id);
      this.notice = 'Đã xóa sản phẩm ' + product.name + '.';
    },
  },
}).mount('#app');
