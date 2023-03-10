
  <html>
    <head>
        <title>CPSC 304 PHP/Oracle Demonstration</title>
    </head>

    <body>
        <h2>Add a Property Type</h2>
        <form method="POST" action= "propertyType.php"> <!--refresh page when submitted-->
            <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
            Property Type ID: <input type="text" name="PropertyTypeID"> <br /><br />
            Listing ID: <input type="text" name="ListingID"> <br /><br />
            Number of Bedrooms: <input type="text" name="BedroomCount"> <br /><br />
            Number of Bathrooms: <input type="text" name="BathroomCount"> <br /><br />
            <input type="submit" value="Add Property Type" name="insertSubmit"></p>
        </form>

        <hr />

        <h2>Update Bedrooms and Bathrooms of Property Type</h2>
        <p>The values are case sensitive and if you enter in the wrong case, the update statement will not do anything.</p>

        <form method="POST" action="propertyType.php"> <!--refresh page when submitted-->
            <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
            Property Type ID: <input type="text" name="PropertyTypeID"> <br /><br />
            <select name="attribute" id="attribute">
                <option value="" selected>--- Choose an Attribute ---</option>
                <option value="BedroomCount" name= "BedroomCount">Number of Bedrooms</option>
                <option value="BathroomCount" name= "BathroomCount">Number of Bathrooms</option>
            </select>
            New Value: <input type="text" name="NewValue"> <br /><br />

            <input type="submit" value="Update" name="updateSubmit"></p>
        </form>

        <hr />

        <h2>Delete a Property Type</h2>
        <form method="GET" action= "propertyType.php"> <!--refresh page when submitted-->
            <input type="hidden" id="deleteTypeRequest" name="deleteTypeRequest">
            Property Type ID: <input type="text" name="PropertyTypeID"> <br /><br />            
            <input type="submit" value="Delete Property" name="deleteTypeRequest"></p>
        </form>

        <hr />

        <h2>Count the Property Type</h2>
        <form method="GET" action="propertyType.php"> <!--refresh page when submitted-->
            <input type="hidden" id="countTupleRequest" name="countTupleRequest">
            <input type="submit" name="countTuples"></p>
        </form>

	    <h2>Display the Property Type</h2>
	    <form method="GET" action= "propertyType.php"> <!--refresh page when submitted-->
	        <input type="hidden" id="displayTupleRequest" name="displayTupleRequest">
	        <input type="submit" name="displayTuples"></p>
	    </form>

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
            echo "<br>Retrieved data from table Property Type:<br>";
            echo "<table>";
            echo "<tr><th>Property Type ID</th><th>Listing ID</th><th>Number of Bedrooms</th><th>Number of Bathrooms</th></tr>";

            // echo "<br>Cannot parse the following command: " . $result . "<br>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td></tr>"; //or just use "echo $row[0]"
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

        function handleUpdateRequest() {
            global $db_conn;
            $attribute =  filter_input(INPUT_POST, 'attribute', FILTER_SANITIZE_STRING);
            echo '<script>console.log("'. $attribute. '")</script>';
            echo '<script>console.log("TEST")</script>';


            $propertyTypeID = $_POST['PropertyTypeID'];
            $newvalue = $_POST['NewValue'];

            // you need the wrap the old name and new name values with single quotations
            executePlainSQL("UPDATE PropertyType SET ". $attribute . "=" . $newvalue . " WHERE PropertyTypeId=" . $propertyTypeID);
            OCICommit($db_conn);
        }

        function handleResetRequest() {
            global $db_conn;
            // Drop old table
            
            OCICommit($db_conn);
        }

        function handleDeleteRequest() {
            global $db_conn;

            //Getting the values from user and insert data into the table
            $propertytype_id = $_GET['PropertyTypeID'];
            //executeBroadSQL("DELETE FROM propertyType WHERE ListingID = (:bind1)", $alltuples)
            executePlainSQL("DELETE FROM PropertyType WHERE PropertyTypeID=" . $propertytype_id);
            OCICommit($db_conn);
        }

        function handleInsertRequest() {
            global $db_conn;

            //Getting the values from user and insert data into the table
            $tuple = array (
                ":bind1" => $_POST['PropertyTypeID'],
                ":bind2" => $_POST['ListingID'],
                ":bind3" => $_POST['BedroomCount'],
                ":bind4" => $_POST['BathroomCount']           
            );
            echo '<script>console.log("Your stuff here")</script>';
            
            $alltuples = array (
                $tuple
            );

            executeBoundSQL("insert into PropertyType values (:bind1, :bind2 , :bind3, :bind4)", $alltuples);
            OCICommit($db_conn);
        }

        function handleCountRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT Count(*) FROM PropertyType");
            echo '<script>console.log("COUNT")</script>';


            if (($row = oci_fetch_row($result)) != false) {
                echo "<br> The number of tuples in PropertyType: " . $row[0] . "<br>";
            }
        }

        function handleDisplayRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT * FROM PropertyType");

            printResult($result);
        }

        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('resetTablesRequest', $_POST)) {
                    handleResetRequest();
                } else if (array_key_exists('updateQueryRequest', $_POST)) {
                    handleUpdateRequest();
                } else if (array_key_exists('insertQueryRequest', $_POST)) {
                    handleInsertRequest();
                }

                disconnectFromDB();
            }
        }


        // HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists('countTuples', $_GET)) {
                    echo '<script>console.log("COUNT req")</script>';
                    handleCountRequest();
                } else if (array_key_exists('displayTuples', $_GET)) {
                    echo '<script>console.log("DISPLAY req")</script>';
		            handleDisplayRequest();
                } else if (array_key_exists('deleteTypeRequest', $_GET)) {
                    echo '<script>console.log("DELETE req")</script>';
                    handleDeleteRequest();

                } 

                disconnectFromDB();
            }
        }

		if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
            echo '<script>console.log("POST")</script>';
            handlePOSTRequest();
        } else if (isset($_GET['countTupleRequest']) || isset($_GET['displayTupleRequest']) || isset($_GET['deleteTypeRequest'])) {
            echo '<script>console.log("GET")</script>';
            handleGETRequest();
        }
		?>
	</body>
</html>