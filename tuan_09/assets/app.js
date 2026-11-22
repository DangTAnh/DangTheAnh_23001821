Vue.createApp({
  data() {
    return {
      products: [],
      pagination: { page: 1, limit: 5, total: 0, total_pages: 1 },
      form: { name: '', price: '', stock: 0 },
      editingId: null,
      currentImage: null,
      selectedFile: null,
      previewUrl: '',
      imageError: '',
      uploadError: '',
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
    previewImage() {
      return this.previewUrl || this.currentImage;
    },
  },
  mounted() {
    this.reloadProducts();
  },
  beforeUnmount() {
    if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
  },
  methods: {
    money(value) {
      return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
    },
    async request(url, options = {}) {
      let response;
      try {
        response = await fetch(url, { cache: 'no-store', ...options });
      } catch (error) {
        throw new Error('Không kết nối được API. Hãy kiểm tra máy chủ PHP.');
      }
      let result;
      try {
        result = await response.json();
      } catch (error) {
        throw new Error('Máy chủ không trả về JSON hợp lệ. Hãy mở trang qua PHP.');
      }
      if (!response.ok || result.success !== true) throw new Error(result.message || 'Yêu cầu thất bại.');
      return result;
    },
    async loadProducts(page = this.pagination.page) {
      this.loading = true;
      try {
        const result = await this.request('api/products.php?page=' + page + '&limit=5');
        if (!Array.isArray(result.data) || !result.pagination) throw new Error('Danh sách sản phẩm không hợp lệ.');
        this.products = result.data;
        // API có thể trả trang cuối nếu trang đang xem vừa bị xóa hết.
        this.pagination = result.pagination;
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
    async changePage(page) {
      if (this.busy || page < 1 || page > this.pagination.total_pages) return;
      this.error = '';
      this.pendingDelete = null;
      await this.loadProducts(page);
    },
    clearSelectedImage(resetInput = true) {
      if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
      this.previewUrl = '';
      this.selectedFile = null;
      if (resetInput && this.$refs.imageInput) this.$refs.imageInput.value = '';
      this.imageError = '';
      this.uploadError = '';
    },
    selectImage(event) {
      const file = event.target.files[0];
      this.clearSelectedImage(false);
      if (!file) return;
      if (!/\.(jpe?g|png)$/i.test(file.name) || !['image/jpeg', 'image/png'].includes(file.type)) {
        event.target.value = '';
        this.imageError = 'Chỉ chấp nhận ảnh JPG, JPEG hoặc PNG.';
        return;
      }
      if (file.size === 0 || file.size > 2 * 1024 * 1024) {
        event.target.value = '';
        this.imageError = 'Ảnh phải có dữ liệu và không quá 2 MB.';
        return;
      }
      this.selectedFile = file;
      this.previewUrl = URL.createObjectURL(file);
    },
    resetForm() {
      this.editingId = null;
      this.currentImage = null;
      this.form = { name: '', price: '', stock: 0 };
      this.formError = '';
      this.clearSelectedImage();
    },
    editProduct(product) {
      if (this.busy) return;
      this.resetForm();
      this.editingId = product.id;
      this.currentImage = product.image;
      this.form = { name: product.name, price: product.price, stock: product.stock };
      this.error = '';
      this.message = '';
      this.pendingDelete = null;
      this.$nextTick(() => this.$refs.nameInput.focus());
    },
    validateForm() {
      this.form.name = this.form.name.trim();
      if (Array.from(this.form.name).length < 1 || Array.from(this.form.name).length > 100) return 'Tên sản phẩm phải từ 1 đến 100 ký tự.';
      if (!Number.isInteger(this.form.price) || this.form.price <= 0 || this.form.price > 2147483647) return 'Đơn giá phải là số nguyên từ 1 đến 2.147.483.647.';
      if (!Number.isInteger(this.form.stock) || this.form.stock < 0 || this.form.stock > 2147483647) return 'Tồn kho phải là số nguyên từ 0 đến 2.147.483.647.';
      return '';
    },
    async uploadImage() {
      const data = new FormData();
      data.append('product_id', String(this.editingId));
      data.append('image', this.selectedFile);
      // Trình duyệt tự đặt Content-Type và boundary cho FormData.
      const result = await this.request('api/upload.php', { method: 'POST', body: data });
      this.currentImage = result.data.image;
    },
    async saveProduct() {
      if (this.busy) return;
      this.error = '';
      this.message = '';
      this.uploadError = '';
      this.formError = this.validateForm();
      if (this.formError) return;
      const isAdding = this.editingId === null;
      this.saving = true;
      this.pendingDelete = null;
      try {
        const result = await this.request('api/products.php' + (isAdding ? '' : '?id=' + this.editingId), {
          method: isAdding ? 'POST' : 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.form),
        });
        // Ghi nhớ id ngay khi JSON đã lưu, tránh POST trùng nếu upload thất bại.
        this.editingId = result.data.id;
        this.currentImage = result.data.image;
        this.message = result.message;
        if (this.selectedFile) {
          try {
            await this.uploadImage();
            this.message += ' Đã upload ảnh.';
          } catch (error) {
            this.uploadError = 'Thông tin sản phẩm đã lưu. Upload ảnh thất bại: ' + error.message + ' Bạn có thể thử lại.';
          }
        }
        if (!this.uploadError) this.resetForm();
        await this.loadProducts(isAdding ? 1 : this.pagination.page);
      } catch (error) {
        this.error = error.message;
      } finally {
        this.saving = false;
      }
    },
    async retryUpload() {
      if (this.busy || !this.selectedFile || this.editingId === null) return;
      this.saving = true;
      this.uploadError = '';
      this.error = '';
      try {
        await this.uploadImage();
        this.message = 'Đã upload lại ảnh thành công.';
        this.resetForm();
        await this.loadProducts();
      } catch (error) {
        this.uploadError = 'Upload ảnh thất bại: ' + error.message + ' Thông tin sản phẩm và ảnh cũ vẫn được giữ.';
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
      this.deleting = true;
      this.error = '';
      this.message = '';
      try {
        const result = await this.request('api/products.php?id=' + id, { method: 'DELETE' });
        this.pendingDelete = null;
        if (this.editingId === id) this.resetForm();
        this.message = result.message;
        await this.loadProducts();
      } catch (error) {
        this.error = error.message;
      } finally {
        this.deleting = false;
      }
    },
  },
}).mount('#app');
