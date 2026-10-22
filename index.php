<?php $tuans = glob('tuan_*', GLOB_ONLYDIR); ?>
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>PHP - XAMPP</title></head>
<body>
<h1>Học PHP - XAMPP</h1>
<ul>
<?php foreach ($tuans as $t): $n = (int)substr($t, 5); ?>
  <li><a href="<?= $t ?>/">Tuần <?= $n ?></a></li>
<?php endforeach; ?>
  <li><a href="midterm/">Bài tập giữa kỳ</a></li>
</ul>
</body>
</html>
