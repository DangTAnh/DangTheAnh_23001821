const fs = require('node:fs');
const vm = require('node:vm');
const assert = require('node:assert/strict');
const path = require('node:path');

function application() {
  let options;
  const context = {
    Vue: { createApp(value) { options = value; return { mount() {} }; } },
    URL: { createObjectURL: () => 'blob:preview', revokeObjectURL() {} },
    FormData, Intl,
  };
  vm.runInNewContext(fs.readFileSync(path.join(__dirname, '../assets/app.js'), 'utf8'), context);
  const app = options.data();
  for (const [key, method] of Object.entries(options.methods)) app[key] = method.bind(app);
  for (const [key, getter] of Object.entries(options.computed)) Object.defineProperty(app, key, { get: getter.bind(app) });
  app.$refs = { imageInput: { value: '' }, nameInput: { focus() {} }, confirmDelete: { focus() {} } };
  app.$nextTick = callback => callback();
  return app;
}

(async () => {
  const app = application();
  app.pagination = { page: 3, limit: 5, total: 12, total_pages: 3 };
  app.form = { name: 'Mẫu', price: 5000, stock: 2 };
  app.selectedFile = new Blob(['PNG'], { type: 'image/png' });
  let jsonWrites = 0;
  let uploads = 0;
  const pages = [];
  app.request = async (url, options = {}) => {
    if (url === 'api/upload.php') {
      uploads++;
      assert.equal(options.body.get('product_id'), '99');
      if (uploads === 1) throw new Error('Lỗi upload kiểm tra');
      return { success: true, data: { id: 99, image: 'uploads/new.png' } };
    }
    if (options.method === 'POST' || options.method === 'PUT') {
      jsonWrites++;
      return { success: true, message: 'Đã lưu.', data: { id: 99, image: null } };
    }
    const page = Number(new URL(url, 'http://localhost').searchParams.get('page'));
    pages.push(page);
    return { success: true, data: [], pagination: { page, limit: 5, total: 13, total_pages: 3 } };
  };
  await app.saveProduct();
  assert.equal(app.editingId, 99);
  assert.ok(app.selectedFile);
  assert.ok(app.uploadError.includes('Thông tin sản phẩm đã lưu'));
  assert.equal(pages[0], 1);
  await app.retryUpload();
  assert.equal(jsonWrites, 1, 'Thử upload lại không được tạo thêm sản phẩm.');
  assert.equal(uploads, 2);
  assert.equal(app.uploadError, '');
  assert.equal(app.editingId, null);

  const edit = application();
  edit.pagination.page = 2;
  edit.editProduct({ id: 4, name: 'Có ảnh', price: 5000, stock: 1, image: 'uploads/old.png' });
  assert.equal(edit.previewImage, 'uploads/old.png');
  edit.selectedFile = new Blob(['PNG'], { type: 'image/png' });
  edit.request = async (url, options = {}) => {
    if (url === 'api/upload.php') throw new Error('Upload hỏng');
    if (options.method === 'PUT') return { success: true, message: 'Đã lưu.', data: { id: 4, image: 'uploads/old.png' } };
    return { success: true, data: [], pagination: { page: 2, limit: 5, total: 6, total_pages: 2 } };
  };
  await edit.saveProduct();
  assert.equal(edit.currentImage, 'uploads/old.png');
  assert.equal(edit.pagination.page, 2);
  assert.equal(edit.editingId, 4);
  edit.selectImage({ target: { files: [{ name: 'a.png', type: 'image/png', size: 2097153 }], value: 'a.png' } });
  assert.ok(edit.imageError.includes('2 MB'));
  assert.equal(edit.selectedFile, null);
  edit.selectImage({ target: { files: [{ name: 'a.txt', type: 'text/plain', size: 10 }], value: 'a.txt' } });
  assert.ok(edit.imageError.includes('JPG'));
  console.log('PASS: upload retry does not duplicate product, failed upload preserves old image, add returns page 1, edit stays on current page, client rejects invalid files.');
})().catch(error => { console.error(error); process.exitCode = 1; });
