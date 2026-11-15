Vue.createApp({
  data() {
    return {
      products: [],
      form: { name: '', price: '', stock: 0 },
      editingId: null,
      pendingDelete: null,
      loading: false,
      saving: false,
      deleting: false,
      error: '',
      formError: '',
      message: '',
    };
  },
  computed: {
    busy() {
      return this.loading || this.saving || this.deleting;
    },
  },
  mounted() {
    this.reloadProducts();
  },
  methods: {
    money(value) {
      return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
    },
    async request(method = 'GET', id = null, data = null) {
      const url = 'api/products.php' + (id === null ? '' : '?id=' + encodeURIComponent(id));
      const options = { method, headers: { Accept: 'application/json' }, cache: 'no-store' };
      if (data !== null) {
        options.headers['Content-Type'] = 'application/json';
        options.body = JSON.stringify(data);
      }
      let response;
      try {
        response = await fetch(url, options);
      } catch (error) {
        throw new Error('Không kết nối được API. Hãy kiểm tra máy chủ PHP và kết nối mạng.');
      }
      let result;
      try {
        result = await response.json();
      } catch (error) {
        throw new Error('Máy chủ không trả về JSON hợp lệ. Hãy mở trang qua máy chủ PHP.');
      }
      if (!response.ok || result.success !== true) {
        throw new Error(result.message || 'Yêu cầu không thành công.');
      }
      return result;
    },
    async loadProducts() {
      this.loading = true;
      try {
        const result = await this.request();
        if (!Array.isArray(result.data)) throw new Error('Danh sách sản phẩm không hợp lệ.');
        this.products = result.data;
        return true;
      } catch (error) {
        this.error = error.message;
        return false;
      } finally {
        this.loading = false;
      }
    },
    async reloadProducts() {
      if (this.busy) return;
      this.error = '';
      this.message = '';
      await this.loadProducts();
    },
    resetForm() {
      this.editingId = null;
      this.form = { name: '', price: '', stock: 0 };
      this.formError = '';
    },
    editProduct(product) {
      if (this.busy) return;
      this.editingId = product.id;
      // Sao chép dữ liệu để sửa trong form không thay đổi dòng trong bảng.
      this.form = { name: product.name, price: product.price, stock: product.stock };
      this.formError = '';
      this.error = '';
      this.message = '';
      this.pendingDelete = null;
      this.$nextTick(() => this.$refs.nameInput.focus());
    },
    validateForm() {
      this.form.name = this.form.name.trim();
      const length = Array.from(this.form.name).length;
      if (length < 1 || length > 100) return 'Tên sản phẩm phải từ 1 đến 100 ký tự.';
      if (!Number.isInteger(this.form.price) || this.form.price <= 0 || this.form.price > 2147483647) {
        return 'Đơn giá phải là số nguyên từ 1 đến 2.147.483.647 đồng.';
      }
      if (!Number.isInteger(this.form.stock) || this.form.stock < 0 || this.form.stock > 2147483647) {
        return 'Tồn kho phải là số nguyên từ 0 đến 2.147.483.647.';
      }
      return '';
    },
    async saveProduct() {
      if (this.busy) return;
      this.error = '';
      this.message = '';
      this.formError = this.validateForm();
      if (this.formError) return;
      this.saving = true;
      this.pendingDelete = null;
      try {
        const result = await this.request(this.editingId === null ? 'POST' : 'PUT', this.editingId, this.form);
        this.resetForm();
        this.message = result.message;
        if (!(await this.loadProducts())) this.message += ' Danh sách chưa được tải lại, hãy thử nút Tải lại danh sách.';
      } catch (error) {
        this.error = error.message;
      } finally {
        this.saving = false;
      }
    },
    requestDelete(product) {
      if (this.busy) return;
      this.pendingDelete = { id: product.id, name: product.name };
      this.error = '';
      this.message = '';
      this.$nextTick(() => this.$refs.confirmDelete.focus());
    },
    async deleteProduct() {
      if (this.busy || !this.pendingDelete) return;
      const id = this.pendingDelete.id;
      this.error = '';
      this.message = '';
      this.deleting = true;
      try {
        const result = await this.request('DELETE', id);
        this.pendingDelete = null;
        if (this.editingId === id) this.resetForm();
        this.message = result.message;
        if (!(await this.loadProducts())) this.message += ' Danh sách chưa được tải lại, hãy thử nút Tải lại danh sách.';
      } catch (error) {
        this.error = error.message;
      } finally {
        this.deleting = false;
      }
    },
  },
}).mount('#app');
