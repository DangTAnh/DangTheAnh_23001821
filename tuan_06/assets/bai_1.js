// Bài 1: createApp, data, nội suy, v-text, v-bind, v-if và v-show.
Vue.createApp({
  data() {
    return {
      student: {
        id: 'SV001',
        name: 'Nguyễn Văn An',
        className: 'CNTT01',
        avatar: 'assets/avatar.svg',
        average: 8.2,
        studying: true,
        introduction: 'Tôi là sinh viên ngành Công nghệ thông tin, hiện đang học lập trình web với VueJS.',
      },
      showIntro: true,
    };
  },
  computed: {
    validScore() {
      return typeof this.student.average === 'number'
        && Number.isFinite(this.student.average)
        && this.student.average >= 0 && this.student.average <= 10;
    },
    rankClass() {
      if (!this.validScore) return 'neutral';
      if (this.student.average >= 8) return 'success';
      if (this.student.average >= 6.5) return 'info';
      if (this.student.average >= 5) return 'warning';
      return 'danger';
    },
  },
}).mount('#app');
