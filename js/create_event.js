$(function(){
  // Ajax送信(イベント作成)
  $("#create-form .btn_submit").on("click", (e) => {
    console.log("success");
    $.ajax({
      type: "POST",
      url: "php/_changeDB.php",
      dataType: "text",
      timeout: 10000,
      data: {
        "command": "create",
        "name": $("#create-form #name").val(),
        "num": $("#create-form #num").val(),
        "kmPl": $("#create-form #kmPl").val(),
        "costPl": $("#create-form #costPl").val(),
        "carUse": $("#carUse").prop("checked"),
        "others": ""
      }
    }).done((data)=>{
      // メインページに移動
      location.href = "mainpage.php?key=" + data;
    }).fail((jqXHR, textStatus, errorThrown) =>{
      $(body).text("エラーが発生しました。");
      console.log("エラー:"+errorThrown.message);
    });
  });
  // 車使用有無変化
  $("#carUse").on("change", (e)=>{
    if ($("#carUse").prop("checked") == true){
      $("#kmPl, #costPl").prop("disabled", false);
    }else{
      $("#kmPl, #costPl").prop("disabled", true);
    }
  });
});