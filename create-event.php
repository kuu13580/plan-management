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
    <div class="form-wrapper">
      <form action="" method="POST" id="create-form">
        <div><label class="" for="name">名前</label><input type="text" id="name" required></div>
        <div><label class="" for="num">人数</label><input type="number" id="num" required></div>
        <div class="form-check form-switch" style = "visibility: hidden">
          <label class="form-check-label" for="carUse">車使用</label>
          <input class="form-check-input" type="checkbox" id="carUse">
        </div>
        <div class="mb-3 me-3 car-options" style = "visibility: hidden">
          <div><label for="kmPl">燃費</label><input type="number" id="kmPl" disabled><span class="unit">km/L</span></label></div>
          <div><label for="costPl">ガソリン価格</label><input type="number" id="costPl" disabled><span class="unit">¥/L</span></div>
        </div>
        <button type="button" class="btn btn_submit">作成</button>
      </form>
    </div>
  </div>
</body>

</html>