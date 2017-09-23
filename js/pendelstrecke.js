console.log('Hi-----');

var linienArr=[];
$linie=$('#linie');
$linie.typeahead({
    source: function(query, proc) {
	$.get('backend/action.php?action=sucheLinien&linie=' + query, proc);
    }});
$linie.change(function() {

    var current = $linie.typeahead("getActive");
    if (current) {
      if (current.name == $linie.val()) {
	  console.log('EXACT' , current.id);
      // This means the exact match is found. Use toLowerCase() if you want case insensitive match.
    } else {
      // This means it is only a partial match, you can either add a new item
      // or take the active if you don't want new items
    }
  } else {
    // Nothing is active so it is a new value (or maybe empty value)
  }
});
