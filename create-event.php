<?php
$title = "イベント作成";
$description = "イベント作成ページ";
include "php/_header.php";
?>
<link rel="stylesheet" href="css/create_event.css">
<script src="js/create_event.js" defer></script>
</head>

<body>
  <div class="container">
  <div class="title">イベント新規作成</div>
    <div class="form-wrapper">
      <form action="" method="POST" id="create-form">
        <div><label class="" for="name">イベント名</label><input type="text" id="name" required></div>
        <div><label class="" for="num">参加人数</label><input type="number" id="num" required></div>
        <div class="form-check form-switch">
          <label class="form-check-label" for="carUse">燃料費概算</label>
          <input class="form-check-input" type="hidden" id="carUse" value="false">
          <span style="font-size: 1rem;margin-left: .5rem;color:red">現在この機能は使用できません</span>
        </div>
        <div class="mb-3 me-3 car-options">
          <div><label for="kmPl">燃費</label><input type="number" id="kmPl" disabled><span class="unit">km/L</span></label></div>
          <div><label for="costPl">ガソリン価格</label><input type="number" id="costPl" disabled><span class="unit">¥/L</span></div>
        </div>
        <div class="btn-wrapper"><button type="button" class="btn btn_submit">作成</button></div>
      </form>
    </div>
  </div>
</body>

</html>