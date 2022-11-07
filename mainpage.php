<?php
$title = "メインページ";
$description = "メインのページ";
include "php/_header.php";
if (!$_GET["key"]) {
    header('Location: error.php');
    exit;
}
$event_key = $_GET["key"];
?>
<link rel="stylesheet" href="css/mainpage.css?ver=1.1.10">
<script src="js/mainpage.js?ver=1.1.8" defer></script>
</head>

<body>
  <header>
    <div class="container">
      <div class="event-title">ここにイベントタイトル</div>
      <button class="modal_open btn" data-target="modal01">スケジュール追加</button>
      <!-- <button class="btn btn_refresh">更新</button> -->
      タグ：<select id="tags">
      </select><br>
      <input type="text" name="addTag" id="addTag">
      <button class="btn btn_addTag">タグ追加</button>
      <button class="btn btn_deleteTag">タグ削除</button>
    </div>
  </header>
  <div class="header-adjust"></div>
  <!-- イベント作成モーダル -->
  <div id="modal01" class="modal_bg">
    <div class="modal_content">
      <div class="form-wrapper container">
        <form action="" method="POST" id="insert-form">
          <div><label for="insert-type">種類</label><label><input type="radio" name="insert-type" value="schedule">予定</label><label><input type="radio" name="insert-type" value="transportation">移動</label></div>
          <div class="schedule-contents"><label for="insert-contents">内容</label><input type="text" id="insert-contents"
              required></div>
          <div class="transportation-contents" style="display: none;"><label for="insert-contents">移動手段</label>
            <?php
          $means = array(
            "walk" => "徒歩",
            "car" => "車",
            "train" => "電車",
            "bus" => "バス",
            "taxi" => "タクシー",
            "airplane" => "飛行機",
            "ship" => "フェリー",
            "bicycle" => "自転車"
          );
foreach ($means as $key => $value) {
    ?>
            <label><input type="radio" name="insert-means"
                value="<?php echo $key ?>"><?php echo $value ?></label>
            <?php
} ?>
          </div>
          <div><label for="insert-stime">開始時間</label><input type="time" id="insert-stime" step="120" required>
          </div>
          <div><label for="insert-duration">所要時間</label><input type="time" id="insert-duration" value="00:00">
          </div>
          <div><label for="insert-cost">費用</label><input type="number" id="insert-cost"></div>
          <div><label for="insert-others">詳細</label><textarea id="insert-others"></textarea></div>
          <button type="button" class="btn btn_submit">作成</button>
        </form>
      </div>
      <div class="modal_close_btn">
        <div>×</div>
      </div>
    </div>
  </div>
  <!-- イベント編集モーダル -->
  <div id="modal02" class="modal_bg">
    <div class="modal_content">
      <div class="form-wrapper container">
        <form action="" method="POST" id="edit-form">
          <input type="hidden" id="edit-id" value="0">
          <input type="hidden" id="edit-type" value="x">
          <div class="schedule-contents"><label>内容</label><input type="text" id="edit-contents" required></div>
          <div class="transportation-contents"><label for="edit-contents">移動手段</label>
            <?php
foreach ($means as $key => $value) {
    ?>
            <label><input type="radio" name="edit-means"
                value="<?php echo $key ?>"><?php echo $value ?></label>
            <?php
} ?>
          </div>
          <div><label for="edit-stime">開始時間</label><input type="time" id="edit-stime" required></div>
          <div><label for="edit-duration">所要時間</label><input type="time" id="edit-duration" value="00:00">
          </div>
          <div><label for="edit-cost">費用</label><input type="number" id="edit-cost"></div>
          <div><label for="edit-others">詳細</label><textarea id="edit-others"></textarea></div>
          <button type="button" class="btn btn_submit">変更</button>
        </form>
      </div>
      <div class="modal_close_btn">
        <div>×</div>
      </div>
    </div>
  </div>
  <section class="container">
    <div id="event-table" data-key="<?php echo $event_key; ?>"></div>
  </section>
</body>