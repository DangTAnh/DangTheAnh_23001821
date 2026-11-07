const TheSanPham = {
  props: {
    ten: { type: String, required: true },
    gia: { type: Number, required: true },
    anh: { type: String, required: true },
    moTa: { type: String, default: 'Chưa có mô tả.' },
    tonKho: { type: Number, default: 0 },
  },
  emits: ['mua'],
  data() {
    // Mỗi instance giữ trạng thái mở mô tả của riêng mình.
    return { showDescription: false };
  },
  methods: {
    buy() {
      if (this.tonKho > 0) this.$emit('mua', this.ten);
    },
  },
  template: `
    <article class="product-card" :aria-label="ten">
      <img :src="anh" :alt="ten" width="120" height="100">
      <h2>{{ ten }}</h2>
      <p>Giá: {{ gia.toLocaleString('vi-VN') }} đ</p>
      <p>Tồn kho: {{ tonKho }} - <span :class="tonKho > 0 ? 'success' : 'error'">{{ tonKho > 0 ? 'Còn hàng' : 'Hết hàng' }}</span></p>
      <div class="actions">
        <button type="button" @click="showDescription = !showDescription" :aria-expanded="showDescription">{{ showDescription ? 'Ẩn mô tả' : 'Xem mô tả' }}</button>
        <button type="button" :disabled="tonKho <= 0" @click="buy">Mua</button>
      </div>
      <p v-show="showDescription">{{ moTa }}</p>
    </article>
  `,
};
