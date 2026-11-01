// Bài 3: v-for, :key, checkbox, computed và modifier sự kiện.
Vue.createApp({
  data() {
    return {
      tasks: [
        { id: 1, name: 'Đọc tài liệu VueJS', completed: true },
        { id: 2, name: 'Hoàn thành bài tập thực hành tuần 6', completed: false },
        { id: 3, name: 'Ôn tập computed và methods', completed: false },
      ],
      nextId: 4,
      newTask: '',
      error: '',
      filter: 'all',
      filters: [
        { value: 'all', label: 'Tất cả' },
        { value: 'pending', label: 'Chưa hoàn thành' },
        { value: 'completed', label: 'Đã hoàn thành' },
      ],
    };
  },
  computed: {
    totalTasks() {
      return this.tasks.length;
    },
    remainingTasks() {
      return this.tasks.filter(task => !task.completed).length;
    },
    completedTasks() {
      return this.totalTasks - this.remainingTasks;
    },
    filteredTasks() {
      if (this.filter === 'pending') return this.tasks.filter(task => !task.completed);
      if (this.filter === 'completed') return this.tasks.filter(task => task.completed);
      return this.tasks;
    },
    emptyTitle() {
      if (!this.totalTasks) return 'Chưa có công việc nào';
      return this.filter === 'pending' ? 'Không có công việc chưa hoàn thành' : 'Chưa có việc đã hoàn thành';
    },
  },
  methods: {
    addTask() {
      const name = this.newTask.trim();
      if (!name) {
        this.error = 'Vui lòng nhập tên công việc, không chỉ chứa khoảng trắng.';
        return;
      }
      this.tasks.push({ id: this.nextId++, name, completed: false });
      this.newTask = '';
      this.error = '';
    },
    deleteTask(id) {
      this.tasks = this.tasks.filter(task => task.id !== id);
    },
    clearCompleted() {
      this.tasks = this.tasks.filter(task => !task.completed);
    },
  },
}).mount('#app');
