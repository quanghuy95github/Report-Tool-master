function operationReport()
{
	var from_date = document.getElementsByName("from-date")[0].value;
	var to_date = document.getElementsByName("to-date")[0].value;
	if (!from_date.match(/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/) || !to_date.match(/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/)) {
		document.getElementById("check").style.display = 'block';
		document.getElementById("check").innerHTML = "Please enter all fields!";
	} else {
		document.getElementById("check").style.display = 'none';
		document.getElementById("check").innerHTML = "";
		window.location = './mvc/controller/OperationController.php?' + 'from-date=' + from_date + '&to-date=' + to_date;
	}
}

function productivityReport()
{
	var from_date = document.getElementsByName("from-date")[0].value;
	var to_date = document.getElementsByName("to-date")[0].value;
	if (!from_date.match(/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/) || !to_date.match(/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/)) {
		document.getElementById("check").style.display = 'block';
		document.getElementById("check").innerHTML = "Please enter all fields!";
	} else {
		document.getElementById("check").style.display = 'none';
		document.getElementById("check").innerHTML = "";
		window.location = './mvc/controller/ProductivityController.php?' + 'from-date=' + from_date + '&to-date=' + to_date;
	}
}

function monthlyReport()
{
	var from_date = document.getElementsByName("from-date")[0].value;
	var to_date = document.getElementsByName("to-date")[0].value;
	var from_date_Obj = new Date(from_date);
	var to_date_Obj = new Date(to_date);
	if (!from_date.match(/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/) || !to_date.match(/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/)) {
		document.getElementById("check").style.display = 'block';
		document.getElementById("check").innerHTML = "Please enter all fields!";
		return false;
	}
	if (from_date_Obj.getMonth() != to_date_Obj.getMonth()) {
		document.getElementById("check").style.display = 'block';
		document.getElementById("check").innerHTML = "Please select report date same month!";
		return false;
	}
	document.getElementById("check").style.display = 'none';
	document.getElementById("check").innerHTML = "";
	window.location = './mvc/controller/MonthlyReportController.php?' + 'from-date=' + from_date + '&to-date=' + to_date;
}