<?php
class TimePrepare
{
    public $start_time_hour = "00";
    public $start_time_minute = "00";
    public $duration_hour = "00";
    public $duration_minute = "00";
    public $duration = "00";
    public $others = "";
    public function replace($str){
      return preg_replace("/\/\/\/(.+)\/\/\/(.+)\/\/\//", "<a href=\"$2\" target=\"_blank\" rel=\"noopener noreferrer\">$1</a>",$str);
    }
    public function setData($row)
    {
        $this->contents = $this->replace(htmlspecialchars($row["contents"]));
        $this->start_time_hour = (int)substr($row["start_time"], 0, 2);
        $this->start_time_minute = substr($row["start_time"], 3, 2);
        $this->duration_hour = substr($row["duration"], 0, 2)  == "00" ? "" : (int)substr($row["duration"], 0, 2)."時間";
        $this->duration_minute = substr($row["duration"], 3, 2) == "00" ? "" : (int)substr($row["duration"], 3, 2)."分";
        switch($row["block_type"]){
          case "schedule":
            $this->duration = substr($row["duration"], 0, 5) == "00:00" ? "" : "所要時間<br>".$this->duration_hour.$this->duration_minute;
            break;
            case "transportation":
            $this->duration = substr($row["duration"], 0, 5) == "00:00" ? "" : $this->duration_hour.$this->duration_minute;
            break;
        }
        $this->others = $row["others"] == null ? "" : "<span>▼詳細</span><div class=\"others-content\">".$this->replace(nl2br(htmlspecialchars($row["others"])))."</div>";
        $this->cost = $row["cost"] == null ? "" : "<div class=\"cost\"><span class=\"pc-only\">▼費用▼</br></span>".$row["cost"]."円</div>";
    }
}
try {
  // ==================================================データベース接続
    include "_connectDB.php";
    $event_key = $_POST["key"];
    if($_POST["tag"] != ""){
      $tag = $_POST["tag"];
    }else{
      $sql = $dbh->prepare("SELECT DISTINCT(tag) FROM blocks");
      $sql->execute();
      $tag = $sql->fetch()[0];
    }
    $sql = $dbh->prepare("SELECT * FROM blocks JOIN block_orders ON blocks.block_id = block_orders.block_id WHERE blocks.event_key = ? AND tag = ? ORDER BY order_num");
    $sql->bindValue(1, $event_key);
    $sql->bindValue(2, $tag);
    $sql->execute();
    $tp = new TimePrepare();
    $order = 0;
    // 出力
    foreach ($sql as $row) {
        $tp->setData($row);
        // ==============================================スケジュールブロック
        if ($row["block_type"] == "schedule") {
            ?>
<div class="schedule-block event-block container"
  id="block-id-<?php echo $row["block_id"]; ?>"
  data-id="<?php echo $row["block_id"]; ?>">
  <div class="schedule-left block-left">
    <div class="handle">
      <div><i class="fa-solid fa-xl fa-arrows-alt-v"></i></div>
    </div>
    <div class="schedule-times">
      <div class="start-time"><span><?php echo $tp->start_time_hour.":".$tp->start_time_minute; ?></span>
      </div>
      <div class="duration"><?php echo $tp->duration; ?>
      </div>
    </div>
    <div class="contents">
      <div class="schedule-title">
        <span><?php echo $tp->contents; ?>
        </span>
      </div>
      <div class="others"><?php echo $tp->others; ?>
      </div>
    </div>
  </div>
  <div class="block-right schedule-right">
    <?php echo $tp->cost; ?>
    <div class="btn_delete btn"><i class="fa-solid fa-trash-can"></i></div>
    <div class="modal_open btn btn_edit" data-target="modal02"><i class="fa-solid fa-pen-to-square"></i></div>
  </div>
</div>
<?php
// ===========================================================移動ブロック
        } elseif ($row["block_type"] == "transportation") {
            $tp->setData($row); ?>
<div class="transportation-block event-block container"
  id="block-id-<?php echo $row["block_id"]; ?>"
  data-id="<?php echo $row["block_id"]; ?>">
  <div class="transportation-left block-left">
    <div class="handle">
      <div><i class="fa-solid fa-xl fa-arrows-alt-v"></i></div>
    </div>
    <div class="start-time"><?php echo $tp->start_time_hour.":".$tp->start_time_minute; ?>
    </div>
    <div class="means"><i class="fa-solid fa-2xl fa-<?php
    $icons = array(
      "walk" => "person-walking",
      "car" => "car",
      "train" => "train-subway",
      "bus" => "bus",
      "taxi" => "taxi",
      "airplane" => "plane",
      "ship" => "ship",
      "bicycle" => "bicycle"
    );
            if (array_key_exists($row["contents"], $icons)) {
                echo $icons[$row["contents"]];
            } else {
                echo "xmark";
            } ?>"></i>
    </div>
    <div class="contents">
      <div class="duration"><?php echo $tp->duration; ?>
      </div>
      <div class="others"><?php echo $tp->others; ?>
      </div>
    </div>
  </div>
  <div class="block-right transportation-right">
    <?php echo $tp->cost; ?>
    <div class="btn_delete btn"><i class="fa-solid fa-trash-can"></i></div>
    <div class="modal_open btn btn_edit" data-target="modal02"><i class="fa-solid fa-pen-to-square"></i></div>
  </div>
</div>
<?php
        } else {
            echo "エラー"."<br>";
        }
    }
    $dbh = null;
} catch (PDOException $e) {
    print('Error:' . $e->getMessage());
    die();
}