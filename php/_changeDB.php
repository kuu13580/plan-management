<?php

include "_connectDB.php";

function convertNull($data)
{
    if ($data === "" or $data === 0) {
        return null;
    } elseif ($data === "00:00" or $data === "00:00:00") {
        return "00:00:00";
    } else {
        return $data;
    }
}
function replace($str)
{
    return preg_replace("/\/\/\/(.+)\/\/\/(.+)\/\/\//", "<a href=\"$2\" target=\"_blank\" rel=\"noopener noreferrer\">$1</a>", $str);
}

try {
    // ===============================イベント作成==========================
    if ($_POST["command"] == "create") {
        $event_key = hash("sha256", $_POST["name"].time());
        $sql = $dbh->prepare("INSERT INTO events_table(event_key, event_name, participant_num, kmPl, costPl, others) VALUE (?, ?, ?, ?, ?, ?)");
        if ($_POST["carUse"] == true) {
            $sql->execute(array($event_key, $_POST["name"], (int)$_POST["num"], convertNull((int)$_POST["kmPl"]), convertNull((int)$_POST["costPl"]), convertNull($_POST["others"])));
        } else {
            $sql->execute(array($event_key, $_POST["name"], (int)$_POST["num"], null, null, convertNull($_POST["others"])));
        }
        $sql = $dbh->prepare("INSERT INTO blocks(event_key, start_time, duration, contents, cost, block_type, others, tag) VALUE (?, \"00:00\", \"00:00\", \"予定を追加\", null, \"schedule\", null, \"1日目\")");
        $sql->execute(array($event_key));
        $sql = $dbh->prepare("INSERT INTO block_orders(event_key, block_id, order_num) VALUE (?, ?, ?)");
        $sql->execute(array($event_key, $dbh->lastInsertId(), 0));
        echo $event_key;
    //================================ブロック作成===========================
    } elseif ($_POST["command"] == "insert") {
        $last = -1024;
        if (isset($_POST["last"])) {
            $last = (int)$_POST["last"];
            $sql = $dbh->prepare("SELECT order_num FROM block_orders WHERE block_id = ?");
            $sql->execute(array($last));
            $last = $sql->fetch()[0];
        }
        $sql = $dbh->prepare("INSERT INTO blocks(event_key, start_time, duration, contents, cost, block_type, others, tag) VALUE (?, ?, ?, ?, ?, ?, ?, ?)");
        $sql->execute(array($_POST["key"], $_POST["stime"], convertNull($_POST["duration"]), $_POST["contents"], convertNull((int)$_POST["cost"]), $_POST["type"], convertNull($_POST["others"]), $_POST["tag"]));
        $sql = $dbh->prepare("INSERT INTO block_orders(event_key, block_id, order_num) VALUE (?, ?, ?)");
        $sql->execute(array($_POST["key"], $dbh->lastInsertId(), $last + 1024));
        $sql = $dbh->prepare("UPDATE events_table SET latest = ? WHERE event_key = ?");
        $sql->execute(array(date("Y-m-d H:i:s"), $_POST["key"]));
        echo "挿入成功";
    //================================ブロック削除===========================
    } elseif ($_POST["command"] == "delete") {
        $block_id = (int)$_POST["block_id"];
        $sql = $dbh->prepare("DELETE FROM blocks WHERE block_id = ?");
        $sql->execute(array($block_id));
        $sql = $dbh->prepare("DELETE FROM block_orders WHERE block_id = ?");
        $sql->execute(array($block_id));
        $sql = $dbh->prepare("UPDATE events_table SET latest = ? WHERE event_key = ?");
        $sql->execute(array(date("Y-m-d H:i:s"), $_POST["key"]));
        echo "削除成功";
    // ================================ブロック変更==========================
    } elseif ($_POST["command"] == "edit") {
        if ($_POST["stage"] == "reference") {
            $block_id = (int)$_POST["block_id"];
            $sql = $dbh->prepare("SELECT * FROM blocks WHERE block_id = ?");
            $sql->execute(array($block_id));
            $data = $sql->fetch();
            header("Content-type: application/json; charset=UTF-8");
            echo json_encode($data);
        } elseif ($_POST["stage"] == "update") {
            $sql = $dbh->prepare("UPDATE blocks SET start_time = ?, duration = ?, contents = ?, cost = ?, others = ? WHERE block_id = ?");
            $sql->execute(array($_POST["stime"], convertNull($_POST["duration"]), $_POST["contents"], convertNull($_POST["cost"]), convertNull($_POST["others"]), (int)$_POST["block_id"]));
            $sql = $dbh->prepare("UPDATE events_table SET latest = ? WHERE event_key = ?");
            $sql->execute(array(date("Y-m-d H:i:s"), $_POST["key"]));
            echo "変更成功";
        } else {
            // えらー
        }
    // ================================順番変更==========================
    } elseif ($_POST["command"] == "exchange") {
        $ary_order = $_POST["ary_order"];
        $order = 0;
        $sql = $dbh->prepare("UPDATE block_orders SET order_num = ? WHERE block_id = ?");
        foreach ($ary_order as $block_id) {
            $sql->execute(array($order, (int)$block_id));
            $order += 1;
        }
        $sql = $dbh->prepare("UPDATE events_table SET latest = ? WHERE event_key = ?");
        $sql->execute(array(date("Y-m-d H:i:s"), $_POST["key"]));
        echo "変更成功";
    // ======================================タグ参照=========================
    } elseif ($_POST["command"] == "tags") {
        $sql = $dbh->prepare("SELECT event_name FROM events_table WHERE event_key = ?");
        $sql->execute(array($_POST["key"]));
        $title = $sql->fetch()[0];
        $sql = $dbh->prepare("SELECT DISTINCT(tag) FROM blocks WHERE event_key = ?");
        $sql->execute(array($_POST["key"]));
        $tag_text = "";
        foreach ($sql as $row) {
            $tag = htmlspecialchars($row["tag"]);
            $tag_text = $tag_text."<option value=\"".$tag."\">".$tag."</option>";
        }
        $data = array(
            "tags" => $tag_text,
            "title" => replace(htmlspecialchars($title))
        );
        header("Content-type: application/json; charset=UTF-8");
        echo json_encode($data);
    } elseif($_POST["command"] == "deleteTags") {
        $sql = $dbh->prepare("DELETE FROM blocks WHERE event_key = ? AND tag = ?");
        $sql->execute(array($_POST["key"], $_POST["tag"]));
}else{
        //=================================GET処理==============================
        echo "失敗";
    }
    exit;
} catch (Exception $e) {
    echo $e;
}