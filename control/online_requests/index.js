$(document).ready(function () {
  // jquery ready

  // РґР»СЏ С„РѕСЂРјС‹ РґРѕР±Р°РІР»РµРЅРёСЏ

  if (isGetVarExists("action") == "addItem") {
    $("#news_form_page_title").focus();
  }

  // РґР»СЏ С„РѕСЂРјС‹ СЂРµРґР°РєС‚РёСЂРѕРІР°РЅРёСЏ

  if (isGetVarExists("action") == "editItem") {
    // $('#news_form_html_code_1').focus();

    if (isGetVarExists("itemID")) {
      // С„РѕСЂРјРёСЂСѓРµРј СЃРїРёСЃРѕРє backup'РѕРІ РґР»СЏ СѓРєР°Р·Р°РЅРЅС‹С… РїРѕР»РµР№

      $.backup({
        table_name: "news",
        entry_id: isGetVarExists("itemID"),
        fields_name: [
          "news_form_page_title",
          "news_form_full_navigation",
          "news_form_h1",
          "news_form_text",
          "news_form_footeranchor",
        ],
      });
    }
  }

  // РґР»СЏ СЃРїРёСЃРєР°

  if (!isGetVarExists("action")) {
    $("#search_by_news").focus();
  }

  // РїРѕРґРєР»СЋС‡Р°РµРј РєР°Р»РµРЅРґР°СЂСЊ (РёРЅРёС†РёР°Р»РёР·Р°С†РёСЏ UI datepicker)

  $("#news_form_date_add").datepicker({
    numberOfMonths: 2,

    dateFormat: "dd.mm.yy",

    showButtonPanel: true,

    // altField: "#date_add_hidden",

    // altFormat: "yy-mm-dd 00:00:00"
  });

  // СЃСѓР±РјРёС‚ С„РѕСЂРјС‹ РґРѕР±Р°РІР»РµРЅРёСЏ РЅРѕРІРѕСЃС‚Рё

  $("#news_form").on("submit", function () {
    if (!checkForm("#news_form")) return false;
  });

  // РёР·РјРµРЅРµРЅРёСЏ РІ РїРѕР»Рµ "РќР°Р·РІР°РЅРёРµ"

  $("#news_form_h1").on("change keyup click", function (event) {
    checkExistenceByName(event);
  });

  function checkExistenceByName(event) {
    var name = $.trim($("#news_form_h1").val());

    // РїРѕР»СѓС‡Р°РµРј СЃС‚Р°СЂРѕРµ Р·РЅР°С‡РµРЅРёРµ

    var old_value = $("#news_form_h1").attr("data-old-value");

    // console.log('name: ' + name + ', old_value:' + old_value);

    // if (name.length && name != old_value) {

    if (name.length && name != old_value) {
      // С„РёРєСЃРёСЂСѓРµРј СЃС‚Р°СЂРѕРµ Р·РЅР°С‡РµРЅРёРµ

      $("#news_form_h1").attr("data-old-value", name);

      // РѕС‚РјРµРЅСЏРµРј РІСЃРµ РїСЂРµРґС‹РґСѓС‰РёРµ ajax-Р·Р°РїСЂРѕСЃС‹

      // $.ajaxQ.abortAll();

      // РїСЂРѕРІСЂСЏРµРј, РµСЃС‚СЊ Р»Рё С€Р°Р±Р»РѕРЅ РІ Р±Р°Р·Рµ СЃ СѓРєР°Р·Р°РЅРЅС‹Рј РёРјРµРЅРµРј

      $.post(
        "/control/news/ajax.php",
        { action: "check_item_for_existence_by_name", name: name },
        function (data) {
          if (data) {
            try {
              var result = JSON.parse(data); // console.log('%o', result);

              if (result["result"] == "exists") {
                $("#news_form_h1_alert_div")
                  .html(
                    'Р’ Р±Р°Р·Рµ СѓР¶Рµ СЃСѓС‰РµСЃС‚РІСѓРµС‚ РЅРѕРІРѕСЃС‚СЊ СЃ Р·Р°РіРѕР»РѕРІРєРѕРј h1 "' +
                      name +
                      '": <a href="/control/news/?action=editItem&itemID=' +
                      result["id"] +
                      '" target="_blank">СЃРјРѕС‚СЂРµС‚СЊ</a>.<br />РџР¶Р», СѓРєР°Р¶РёС‚Рµ РґСЂСѓРіРѕРµ РЅР°Р·РІР°РЅРёРµ РґР»СЏ РЅРѕРІРѕСЃС‚Рё.'
                  )
                  .removeClass("hidden");

                $("#news_form_h1").focus();
              } else $("#news_form_h1_alert_div").html("").addClass("hidden");
            } catch (err) {
              console.log(err.message);
            }
          } else $("#news_form_h1_alert_div").html("").addClass("hidden");
        }
      );
    }
  }

  // РїСЂРё РѕС€РёР±РєРµ РґРѕР±Р°РІР»РµРЅРёСЏ РЅРѕРІРѕСЃС‚Рё, РІС‹РґРµР»Р°РµРј РЅСѓР¶РЅС‹Р№ option РІ select'Рµ

  if (
    isGetVarExists("action") == "addItemSubmit" &&
    $(".alert.alert-danger").html().length > 0
  ) {
    $("#news_form_cars_types_id option").attr("selected", false);

    $(
      '#news_form_cars_types_id option[value="' +
        $("#news_form_cars_types_id").attr("data-selected") +
        '"]'
    ).attr("selected", "selected");
  }

  // РїСЂРё СѓСЃРїРµС€РЅРѕРј РґРѕР±Р°РІР»РµРЅРёРё РЅРѕРІРѕСЃС‚Рё СЃРєСЂС‹РІР°РµРј СЃРѕРѕР±С‰РµРЅРёРµ РѕР± СѓСЃРїРµС€РЅРѕРј РґРѕР±Р°РІР»РµРЅРёРё С‡РµСЂРµР· 5 СЃРµРє

  if ($(".col-lg-12 .alert.alert-success").length) {
    if ($(".col-lg-12 .alert.alert-success").html().length > 0)
      setTimeout(function () {
        $(".col-lg-12 .alert.alert-success").slideUp();
      }, 5000);
  }

  // РїРѕРёСЃРє

  $("#search_by_news").bind("keyup click", function () {
    var q = $.trim($("#search_by_news").val());

    var old_data = $("#search_by_news").attr("old-data");

    if (q.length > 0 && (!old_data || old_data != q)) {
      $.ajaxQ.abortAll(); // РѕС‚РјРµРЅСЏРµРј РІСЃРµ ajax-Р·Р°РїСЂРѕСЃС‹

      $("#resultSet").html("");

      $.post(
        "/control/news/ajax.php",
        { action: "search", q: q },
        function (data) {
          $("#resultSet").html(data);

          $("#search_by_news").attr("old-data", q);
        }
      );
    }
  });
}); // /jquery ready
