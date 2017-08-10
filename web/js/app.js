function thingsInCommon(button_id) {
    $("button#"+button_id).attr("disabled", true);
    $("button#"+button_id).children("div.hidden").removeClass("hidden");
}

function onNextClick() {
    var fileURL="../Session "
    window.open("../Session 2/icebreaker.html", "_self");        
}      