function fillStation($ele, data) {
    $ele.html('');
    $.each(data, function (idx, ele) {
	$ele.append($('<option>').attr('value',ele.id).text(ele.name));
    });
    $ele.append($('<option>').attr('value',18).text('Du'));
}

function fillStationById(id) {
    // backend/action.php?action=sucheHaltestellen&linienId=1292
    $.get('backend/action.php?action=sucheHaltestellen&linienId='+id, function (data) {
//$.get('JSON.json', function (data) {
	fillStation($('#startHalt'),data);
	fillStation($('#stopHalt'),data);
    });
}
var linienId;
$linie=$('#linie');
$linie.typeahead({
    source: function(query, proc) {
	$.get('backend/action.php?action=sucheLinien&linie=' + query, proc);
    }});
$linie.change(function() {
    var current = $linie.typeahead("getActive");
    if (current) {
      if (current.name == $linie.val()) {
	  // This means the exact match is found. Use toLowerCase() if you want case insensitive match.#
	  fillStationById(current.id);
	  linienId=current.id;
    } else {
	// This means it is only a partial match, you can either add a new item
	// or take the active if you don't want new items
    }
    } else {
      // Nothing is active so it is a new value (or maybe empty value)
    }
});

$('#zeitVon').timepicker({
    showMeridian: false,
    defaultTime: false
});
$('#zeitBis').timepicker({
    showMeridian: false,
    defaultTime: false
});
