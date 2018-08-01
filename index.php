<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Report</title>
	<link rel="stylesheet" type="text/css" href="public/css/style.css">
</head>
<body>
	<div class="form-style">
		<h2>Report</h2>
		    <form method="GET" id="form" action="">
		    	<label for="check" class="check-text" id="check" style="display: none;"></label><br>
		    	<label for="from-date" class="edit-text">From Date: </label>
			    <input type="date" name="from-date" id="from-date" style="margin-left: 20px" required /><br>
			    <label for="to-date" class="edit-text">To Date: </label>
			    <input type="date" name="to-date" id="to-date" style="margin-left: 40px" required /><br>
			    <input type="button" value="Operation" onclick="operationReport()">
			    <input type="button" value="Productivity" onclick="productivityReport()">
			    <input type="button" value="Monthly Report" onclick="monthlyReport()">
		    </form>
	</div>
</body>
<script language="JavaScript" type="text/javascript" src="public/js/script.js"></script>
</html>
