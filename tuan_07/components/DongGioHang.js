const DongGioHang = {
  props: {
    ma: { type: Number, required: true },
    ten: { type: String, required: true },
    donGia: { type: Number, required: true },
    soLuong: { type: Number, required: true },
  },
  emits: ['doi-so-luong', 'yeu-cau-xoa'],
  computed: {
    thanhTien() {
      return this.donGia * this.soLuong;
    },
  },
  methods: {
    money(value) {
      return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
    },
    requestQuantity(quantity) {
      // Chỉ gửi yêu cầu lên cha, không thay đổi props trong component con.
      this.$emit('doi-so-luong', this.ma, quantity);
    },
  },
  template: `
    <tr>
      <td>{{ ten }}</td>
      <td class="price">{{ money(donGia) }}</td>
      <td>
        <div class="quantity">
          <button type="button" :disabled="soLuong <= 1" @click="requestQuantity(soLuong - 1)" :aria-label="'Giảm ' + ten">−</button>
          <span>{{ soLuong }}</span>
          <button type="button" :disabled="soLuong >= 99" @click="requestQuantity(soLuong + 1)" :aria-label="'Tăng ' + ten">+</button>
        </div>
      </td>
      <td class="price">{{ money(thanhTien) }}</td>
      <td><button type="button" @click="$emit('yeu-cau-xoa', ma)" :aria-label="'Xóa ' + ten">Xóa</button></td>
    </tr>
  `,
};
