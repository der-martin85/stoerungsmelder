function fillStation($ele, data) {
    $ele.html('');
    $.each(data, function (idx, ele) {
	$ele.append($('<option>').attr('value',ele.id).text(ele.name));
    });
}

function fillStationById(id) {
    $.get('backend/action.php?action=sucheHaltestellen&linienId='+id, function (data) {
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
$('#btnSave').click(function (event) {
    event.preventDefault();
    console.log('CLICK');
    console.log(linienId);
    console.log($('#zeitVon').val());
    console.log($('#zeitBis').val());
    var days = [];
    $('#weekdays :checked').each(function() {
	days.push($(this).attr('val'));
    });
    console.log(days)
    console.log($('#startHalt').val());
    console.log($('#stopHalt').val());
    console.log($('#warnung').val());
    console.log($('#warnart').val());
    console.log($('#warnzeit').val());
})
