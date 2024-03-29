$(function () {
  event_key = $("#event-table").data("key");
  // ================================テーブル更新
  function refresh() {
    $.ajax({
      type: "POST",
      url: "php/_show.php",
      dataType: "text",
      timeout: 10000,
      data: {
        "key": event_key,
        "page": $("#pages").val()
      }
    }).done((data) => {
      $("#event-table").html(data);
    }).fail((jqXHR, textStatus, errorThrown) => {
      console.log("エラー:" + errorThrown.message);
    });
  }
  $(".btn-refresh").on("click", refresh);
  $("#pages").on("change", () => {
    refresh();
    localStorage.setItem("page-" + event_key.substring(0, 4), $("#pages").val());
  });
  // タグに関する初期化処理
  $.ajax({
    type: "POST",
    url: "php/_changeDB.php",
    dataType: "json",
    timeout: 10000,
    data: {
      "command": "pages",
      "key": event_key
    }
  }).done((data) => {
    $("#pages").html(data["pages"]);
    if (localStorage.getItem("page-" + event_key.substring(0, 4)) == null) {
      $("#pages").prop("selectedIndex", 0);
    } else {
      $("#pages").val(localStorage.getItem("page-" + event_key.substring(0, 4)));
    }
    $(".event-title").html(data["title"])
    document.title = data["title"] + "|たびしぇあ";
    refresh();
  }).fail((jqXHR, textStatus, errorThrown) => {
    console.log("エラー:" + errorThrown.message);
  });
  // ========================データベース更新確認
  latest = "x";
  function updateCheck() {
    $.ajax({
      type: "POST",
      url: "php/_checkUpdate.php",
      dataType: "json",
      timeout: 10000,
      data: {
        "key": event_key,
        "latest": latest
      }
    }).done((data) => {
      if (data["renew"] == true) {
        refresh();
        latest = data["latest"];
      }
    }).fail((jqXHR, textStatus, errorThrown) => {
      console.log("エラー:" + errorThrown.message);
    });
  }
  setInterval(updateCheck, 5000)
  // ====================================ブロック作成
  $("#insert-form .btn-submit").on("click", (e) => {
    let type = $("input[name=insert-type]:checked").val();
    let contents;
    switch (type) {
      case "transportation":
        contents = $("input[name=insert-means]:checked").val();
        break;
      case "schedule":
        contents = $("#insert-contents").val();
        break;
    }
    let last = $("div.event-block:last-child").data("id");
    last = !(last) ? 0 : last;
    let data = {
      "command": "insert",
      "key": event_key,
      "page": $("#pages").val(),
      "stime": $("#insert-stime").val(),
      "duration": $("#insert-duration").val(),
      "contents": contents,
      "cost": $("#insert-cost").val(),
      "type": $("input[name=insert-type]:checked").val(),
      "others": $("#insert-others").val(),
      "last": last
    };
    if (check_input(data) == true) {
      $.ajax({
        type: "POST",
        url: "php/_changeDB.php",
        dataType: "text",
        timeout: 10000,
        data: data
      }).done((data) => {
        $("#insert-form")[0].reset();
        refresh();
      }).fail((jqXHR, textStatus, errorThrown) => {
        console.log("エラー:" + errorThrown.message);
      });
    }
  });
  // ======================================ブロック削除
  $("#event-table").on("click", ".btn-delete", (e) => {
    if (window.confirm("削除してもよろしいですか？")) {
      $.ajax({
        type: "POST",
        url: "php/_changeDB.php",
        dataType: "text",
        timeout: 10000,
        data: {
          "command": "delete",
          "key": event_key,
          "block_id": $(e.currentTarget).parents(".event-block").data("id")
        }
      }).done((data) => {
        refresh();
      }).fail((jqXHR, textStatus, errorThrown) => {
        console.log("エラー:" + errorThrown.message);
      });
    }
  });
  // =========================================イベント作成モーダル
  let winScroll;
  $("input[name=insert-type]").on("change", () => {
    if ($("input[name=insert-type]:checked").val() == "schedule") {
      $(".schedule-contents").show();
      $(".transportation-contents").hide();
    } else if ($("input[name=insert-type]:checked").val() == "transportation") {
      $(".schedule-contents").hide();
      $(".transportation-contents").show();
    }
  });
  $(".modal-open").each(function () {
    $(this).click(() => {
      const target = $(this).data("target");
      const modal = document.getElementById(target);
      $(modal).fadeIn();
      // 背景スクロール禁止
      const scroll_bar = window.innerWidth - $(window).width();
      $("body").css("overflow", "hidden");
      $("body").css("padding-right", scroll_bar);
      // スクロールバー設定
      const modal_content = $(modal).children(".modal-content");
      if ($(modal_content).height() > window.innerHeight * 0.8) {
        $(modal_content).css("overflow-y", "scroll");
      } else {
        $(modal_content).css("overflow-y", "hidden");
      }
      return false;
    });
    $(".modal-bg, .modal-close-btn").click(() => {
      $(".modal-bg").fadeOut();
      $("body").css("padding-right", 0);
      $("body").css("overflow", "auto");
      return false;
    });
    $(".modal-content").click((event) => {
      event.stopPropagation();
    });
  });
  // =============================================ブロック編集モーダル
  $("#event-table").on("click", ".btn-edit", (e) => {
    let block_id = $(e.currentTarget).parents(".event-block").data("id");
    const modal = document.getElementById("modal02");
    $(modal).fadeIn();
    // 背景スクロール禁止
    const scroll_bar = window.innerWidth - $(window).width();
    $("body").css("overflow", "hidden");
    $("body").css("padding-right", scroll_bar);
    // スクロールバー設定
    const modal_content = $(modal).children(".modal-content");
    if ($(modal_content).height() > window.innerHeight * 0.8) {
      $(modal_content).css("overflow-y", "scroll");
    } else {
      $(modal_content).css("overflow-y", "hidden");
    }
    $.ajax({
      type: "POST",
      url: "php/_changeDB.php",
      dataType: "json",
      timeout: 10000,
      data: {
        "command": "edit",
        "stage": "reference",
        "block_id": block_id
      }
    }).done((data) => {
      $("#edit-id").val(block_id);
      $("#edit-stime").val(data["start_time"]);
      $("#edit-duration").val(data["duration"]);
      $("#edit-cost").val(data["cost"]);
      $("#edit-others").val(data["others"]);
      // typeによる変更
      switch (data["block_type"]) {
        case "transportation":
          $(".schedule-contents").hide();
          $(".transportation-contents").show();
          $("input[name=edit-means]").val([data["contents"]]);
          $("#edit-type").val("transportation");
          break;
        case "schedule":
          $(".schedule-contents").show();
          $(".transportation-contents").hide();
          $("#edit-contents").val([data["contents"]]);
          $("#edit-type").val("schedule");
          break;
      }
    }).fail((jqXHR, textStatus, errorThrown) => {
      console.log("エラー:" + errorThrown.message);
    });
    return false;
  });
  // =======================================================ブロック編集
  $("#edit-form .btn-submit").on("click", (e) => {
    let contents;
    switch ($("#edit-type").val()) {
      case "transportation":
        contents = $("input[name=edit-means]:checked").val();
        break;
      case "schedule":
        contents = $("#edit-contents").val();
        break;
    }
    let data = {
      "command": "edit",
      "stage": "update",
      "key": event_key,
      "block_id": $("#edit-id").val(),
      "stime": $("#edit-stime").val(),
      "duration": $("#edit-duration").val(),
      "contents": contents,
      "cost": $("#edit-cost").val(),
      "others": $("#edit-others").val()
    };
    if (check_input(data) == true) {
      $.ajax({
        type: "POST",
        url: "php/_changeDB.php",
        dataType: "text",
        timeout: 10000,
        data: data
      }).done((data) => {
        refresh();
        $(".modal-bg").click();
      }).fail((jqXHR, textStatus, errorThrown) => {
        console.log("エラー:" + errorThrown.message);
      });
    }
  });
  // =================================================順番変更
  $("#event-table").sortable({
    handle: "div.handle",
    delay: 300,
    update: () => {
      let ary_order = $('#event-table').sortable("toArray").map((value, index) => {
        return Number(value.substring(9));
      });
      $.ajax({
        type: "POST",
        url: "php/_changeDB.php",
        dataType: "text",
        timeout: 10000,
        data: {
          "command": "exchange",
          "key": event_key,
          "ary_order": ary_order
        }
      }).done((data) => {
        refresh();
      }).fail((jqXHR, textStatus, errorThrown) => {
        console.log("エラー:" + errorThrown.message);
      });
    }
  });
  // =========================================スケジュールブロック詳細
  $("#event-table").on("click", ".others span", (e) => {
    $(e.currentTarget).next().slideToggle();
  });
  // =========================================タグ追加========
  $(".btn-addPage").on("click", () => {
    let page = $("#addPage").val();
    let data = {
      "command": "insert",
      "key": event_key,
      "page": page,
      "stime": "00:00",
      "duration": "00:00",
      "contents": "予定を追加",
      "cost": "",
      "type": "schedule",
      "others": ""
    };
    if (check_input(data) == true) {
      $.ajax({
        type: "POST",
        url: "php/_changeDB.php",
        dataType: "text",
        timeout: 10000,
        data: data
      }).done((data) => {
        console.log("2");
        $("#pages").append(`<option value=\"${page}\">${page}</option>`);
        $("#pages").val(page);
        refresh();
      }).fail((jqXHR, textStatus, errorThrown) => {
        console.log("エラー:" + errorThrown.message);
      });
    }
  });
  // =========================================タグ削除
  $(".btn-deletePage").on("click", () => {
    if (window.confirm("ページを削除してもよろしいですか？")) {
      let page = $("#pages").val();
      let data = {
        "command": "deletePages",
        "key": event_key,
        "page": page
      };
      if (check_input(data) == true) {
        $.ajax({
          type: "POST",
          url: "php/_changeDB.php",
          dataType: "text",
          timeout: 10000,
          data: data
        }).done((data) => {
          $(`#pages option[value=${page}]`).remove();
          refresh();
        }).fail((jqXHR, textStatus, errorThrown) => {
          console.log("エラー:" + errorThrown.message);
        });
      }
    }
  });
});