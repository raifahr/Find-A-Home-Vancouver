<!-- Credits for file:
  Test Oracle file for UBC CPSC304 2018 Winter Term 1
  Created by Jiemin Zhang
  Modified by Simona Radu
  Modified by Jessica Wong (2018-06-22)
  This file shows the very basics of how to execute PHP commands
  on Oracle.
  Specifically, it will drop a table, create a table, insert values
  update values, and then query for values

  IF YOU HAVE A TABLE CALLED "demoTable" IT WILL BE DESTROYED

  The script assumes you already have a server set up
  All OCI commands are commands to the Oracle libraries
  To get the file to work, you must place it somewhere where your
  Apache server can run it, and you must rename it to have a ".php"
  extension.  You must also change the username and password on the
  OCILogon below to be your ORACLE username and password

	findAHomeVancouver website made by Raifah Rahman and Sarah Li-->

	<html>
    <head>
        <title>CPSC 304 PHP/Oracle Demonstration</title>
    </head>

    <body>

        <h2>Reset</h2>
        <p>If you wish to reset your search results press on the reset button. If this is the first time you're running this page, you MUST use reset</p>

        <form method="POST" action="findAProperty.php">
            <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->
            <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
            <p><input type="submit" value="Reset" name="reset"></p>
        </form>

        <hr />

        <h2>Find a Property/Real Estate Agent With...</h2>
        <p>Remember to type appropriate values for attribute types!</p>
        <form method="GET" action="findAProperty.php"> <!--refresh page when submitted-->
            <input type="hidden" id="searchRequest" name="searchRequest">

            <!-- drop-down table for user projection -->
            <p>A:
            <select name="attributeTable" id="attributeTable">
                <option value="" selected>--- Choose a Table ---</option>
                <option value="PropertyListing">PropertyListing</option>
                <option value="RealEstateAgent">RealEstateAgent</option>
            <select> 
            </p>
            
            <p>With:
            <select name="attribute1" id="attribute1">
                <option value="" selected>--- Choose an Attribute ---</option>
                <optgroup label="PropertyListing">
                    <option value="ListingPrice">ListingPrice</option>
                    <option value="SquareFootage">SquareFootage</option>
                    <option value="CityName">CityName</option>
                </optgroup>
                <optgroup label="RealEstateAgent">
                    <option value="REAName">REAName</option>
                    <option value="Brokerage">Brokerage</option>
                    <option value="Rate">Rate</option>
                </optgroup>
            </select>
            
            <select name="op1" id="op1">
                <option value="" selected>--- Choose an Operator ---</option>
                <option value="<"><</option>
                <option value="<="><=</option>
                <option value="=">=</option>
                <option value=">">></option>
                <option value=">=">>=</option>
            </select>

            <!-- User enter selection condiiton value here: -->
            Enter Value:<input type="text" name="value1"></p>

            <p>With:
            <select name="attribute2" id="attribute2">
                <option value="" selected>--- Choose an Attribute ---</option>
                <optgroup label="PropertyListing">
                    <option value="ListingPrice">ListingPrice</option>
                    <option value="SquareFootage">SquareFootage</option>
                    <option value="CityName">CityName</option>
                </optgroup>
                <optgroup label="RealEstateAgent">
                    <option value="REAName">REAName</option>
                    <option value="Brokerage">Brokerage</option>
                    <option value="Rate">Rate</option>
                </optgroup>
            </select>
            
            <select name="op2" id="op2">
                <option value="" selected>--- Choose an Operator ---</option>
                <option value="<"><</option>
                <option value="<="><=</option>
                <option value="=">=</option>
                <option value=">">></option>
                <option value=">=">>=</option>
            </select>

            <!-- User enter selection condiiton value here: -->
            Enter Value:<input type="text" name="value2"></p>

            <input type="submit" value="Search" name="searchRequest"></p>
        </form>

        <hr />  

        <h2>Filter Square Footage and Listing Price by...</h2>
        <form method="GET" action= "findAProperty.php"> <!--refresh page when submitted-->
            <input type="hidden" id="columnsRequest" name="columnsRequest">   
            <select name="attribute" id="attribute">
                <option value="" selected>--- Choose an Attribute ---</option>
                <option value="CityName">CityName</option>
                <option value="PropertyAddress">PropertyAddress</option>
                <option value="ListingDate">ListingDate</option> 
            <select>                
            <input type="submit" value="Search" name="columnsRequest"></p>
        </form>
        
        <hr />

        <?php
		//this tells the system that it's no longer just parsing html; it's now parsing PHP

        $success = True; //keep track of errors so it redirects the page only if there are no errors
        $db_conn = NULL; // edit the login credentials in connectToDB()
        $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

        function debugAlertMessage($message) {
            global $show_debug_alert_messages;
            if ($show_debug_alert_messages) {
                echo "<script type='text/javascript'>alert('" . $message . "');</script>";
            }
        }

        function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
            //echo "<br>running ".$cmdstr."<br>";
            global $db_conn, $success;

            $statement = OCIParse($db_conn, $cmdstr);
            //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
                echo htmlentities($e['message']);
                $success = False;
            }

            $r = OCIExecute($statement, OCI_DEFAULT);
            if (!$r) {
                echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
                echo htmlentities($e['message']);
                $success = False;
            }

			return $statement;
		}

        function executeBoundSQL($cmdstr, $list) {
            /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
		In this case you don't need to create the statement several times. Bound variables cause a statement to only be
		parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
		See the sample code below for how this function is used */

			global $db_conn, $success;
			$statement = OCIParse($db_conn, $cmdstr);

            if (!$statement) {
                echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                $e = OCI_Error($db_conn);
                echo htmlentities($e['message']);
                $success = False;
            }

            foreach ($list as $tuple) {
                foreach ($tuple as $bind => $val) {
                    //echo $val;
                    //echo "<br>".$bind."<br>";
                    OCIBindByName($statement, $bind, $val);
                    unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
				}

                $r = OCIExecute($statement, OCI_DEFAULT);
                if (!$r) {
                    echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                    $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
                    echo htmlentities($e['message']);
                    echo "<br>";
                    $success = False;
                }
            }
        }

        function printResult($result) { //prints results from a select statement

            $attribute1 = $_GET['attribute1'];
            echo '<script>console.log("'. $attribute1. '")</script>';
            $attribute2 = $_GET['attribute2'];
            echo '<script>console.log("'. $attribute2. '")</script>';

            echo "<br>Your Search Results:<br>";
            echo "<table>";
            echo "<tr><th>$attribute1</th><th>$attribute2</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>";
            }

            echo "</table>";
        }

        function printCityNameResult($result) { //prints results from a select statement
            echo "<br>Your Search Results:<br>";
            echo "<table>";
            echo "<tr><th>City</th><th>Square Footage</th><th>Listing Price</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>"; //or just use "echo $row[0]"
                // echo "<tr><td>" . $result . "</td></tr>";
                // echo "<tr><td>" . $row[0] . "</td></tr>";
            }

            echo "</table>";
        }

        function printListingDateResult($result) { //prints results from a select statement
            echo "<br>Your Search Results:<br>";
            echo "<table>";
            echo "<tr><th>Listing Date</th><th>Square Footage</th><th>Listing Price</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>"; //or just use "echo $row[0]"
                // echo "<tr><td>" . $result . "</td></tr>";
                // echo "<tr><td>" . $row[0] . "</td></tr>";
            }

            echo "</table>";
        }

        function printPropertyAddressResult($result) { //prints results from a select statement
            echo "<br>Your Search Results:<br>";
            echo "<table>";
            echo "<tr><th>Address</th><th>Square Footage</th><th>Listing Price</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>"; //or just use "echo $row[0]"
                // echo "<tr><td>" . $result . "</td></tr>";
                // echo "<tr><td>" . $row[0] . "</td></tr>";
            }

            echo "</table>";
        }

        function connectToDB() {
            global $db_conn;

            // Your username is ora_(CWL_ID) and the password is a(student number). For example,
			// ora_platypus is the username and a12345678 is the password.
            $db_conn = OCILogon("ora_raifahr", "a29435245", "dbhost.students.cs.ubc.ca:1522/stu");

            if ($db_conn) {
                debugAlertMessage("Database is Connected");
                return true;
            } else {
                debugAlertMessage("Cannot connect to Database");
                $e = OCI_Error(); // For OCILogon errors pass no handle
                echo htmlentities($e['message']);
                return false;
            }
        }

        function disconnectFromDB() {
            global $db_conn;

            debugAlertMessage("Disconnect from Database");
            OCILogoff($db_conn);
        }

        function handleResetRequest() {
            global $db_conn;
            // Drop old table
           
            OCICommit($db_conn);
        }

        function handleSearchRequest() {
            global $db_conn;

            //Getting the values from user to grab tuples from table
            $attributeTable =  filter_input(INPUT_GET, 'attributeTable', FILTER_SANITIZE_STRING);
            echo '<script>console.log("'. $attributeTable. '")</script>';

            $attribute1 = $_GET['attribute1'];
            echo '<script>console.log("'. $attribute1. '")</script>';

            $op1 = $_GET['op1'];
            echo '<script>console.log("'. $op1 . '")</script>';

            $value1 = $_GET['value1'];
            echo '<script>console.log("'. $value1 . '")</script>';

            $attribute2 = $_GET['attribute2'];
            echo '<script>console.log("'. $attribute1. '")</script>';

            $op2 = $_GET['op2'];
            echo '<script>console.log("'. $op1 . '")</script>';

            $value2 = $_GET['value2'];
            echo '<script>console.log("'. $value1 . '")</script>';

            
            $result = executePlainSQL("SELECT $attribute1, $attribute2
                                       FROM $attributeTable
                                       WHERE  $attribute1  $op1 '$value1' AND $attribute2 $op2 '$value2'");
            printResult($result);
            
            OCICommit($db_conn);
        }

        function handleColumnsRequest() {
            global $db_conn;

            // //Getting the values from user to grab tuples from table
            $attribute =  filter_input(INPUT_GET, 'attribute', FILTER_SANITIZE_STRING);
            echo '<script>console.log("'. $attribute. '")</script>';
            
            if ($attribute == "CityName") {
                $result = executePlainSQL("SELECT CityName, SquareFootage, ListingPrice FROM PropertyListing ");
                printCityNameResult($result);
            }

            if ($attribute == "PropertyAddress") {
                $result = executePlainSQL("SELECT PropertyAddress, SquareFootage, ListingPrice FROM PropertyListing");
                printPropertyAddressResult($result);
            }

            if ($attribute == "ListingDate") {
                $result = executePlainSQL("SELECT ListingDate, SquareFootage, ListingPrice FROM PropertyListing");
                printListingDateResult($result);
            }
            
            OCICommit($db_conn);
        }



        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('resetTablesRequest', $_POST)) {
                    handleResetRequest();
                }
                disconnectFromDB();
            }
        }

        // HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('countTuples', $_GET)) {
                    handleCountRequest();
                } else if (array_key_exists('displayTuples', $_GET)) {
		            handleDisplayRequest();
                } else if (array_key_exists('searchRequest', $_GET)) {
                    echo '<script>console.log("GET SEARCH REQUEST req")</script>';
                    handleSearchRequest();
                }  else if (array_key_exists('columnsRequest', $_GET)) {
                    echo '<script>console.log("GET get columns req")</script>';
                    handleColumnsRequest();
                } 

                disconnectFromDB();
            }
        }

		if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_GET['columnsRequest']) || isset($_GET['displayTupleRequest']) || isset($_GET['searchRequest'] )) {
            handleGETRequest();
        }
		?>
	</body>
</html>