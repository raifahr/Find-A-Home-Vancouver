
  <html>
    <head>
        <title>CPSC 304 PHP/Oracle Demonstration</title>
    </head>

    <body>
        <h2>Add a Property Listing</h2>
        <form method="POST" action= "propertyListing.php"> <!--refresh page when submitted-->
            <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
            Listing ID: <input type="text" name="ListingID"> <br /><br />
            City: <input type="text" name="CityName"> <br /><br />
            Seller ID: <input type="text" name="SellerID"> <br /><br />
            Agent ID: <input type="text" name="AgentID"> <br /><br />
            Address: <input type="text" name="PropertyAddress"> <br /><br />
            ListingPrice: <input type="text" name="ListingPrice"> <br /><br />
            PropertyStatus: <input type="text" name="PropertyStatus"> <br /><br />
            SquareFootage: <input type="text" name="SquareFootage"> <br /><br />
            ListingDate: <input type="text" name="ListingDate"> <br /><br />
            
            <input type="submit" value="Add Property" name="insertSubmit"></p>
        </form>

        <hr />

        <h2>Update Status of Property Listing</h2>
        <p>The values are case sensitive and if you enter in the wrong case, the update statement will not do anything.</p>

        <form method="POST" action="propertyListing.php"> <!--refresh page when submitted-->
            <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
            Listing ID: <input type="text" name="ListingID"> <br /><br />
            Status: <input type="text" name="Status"> <br /><br />

            <input type="submit" value="Update" name="updateSubmit"></p>
        </form>

        <hr />

        <h2>Delete a Property Listing</h2>
        <form method="GET" action= "propertyListing.php"> <!--refresh page when submitted-->
            <input type="hidden" id="deletePropertyRequest" name="deletePropertyRequest">
            Listing ID: <input type="text" name="ListingID"> <br /><br />            
            <input type="submit" value="Delete Property" name="deletePropertyRequest"></p>
        </form>

        <hr />

        <h2>Count the Property Listings</h2>
        <form method="GET" action="propertyListing.php"> <!--refresh page when submitted-->
            <input type="hidden" id="countTupleRequest" name="countTupleRequest">
            <input type="submit" name="countTuples"></p>
        </form>

	    <h2>Display the Property Listings</h2>
	    <form method="GET" action= "propertyListing.php"> <!--refresh page when submitted-->
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
            echo "<br>Retrieved data from table PropertyListing:<br>";
            echo "<table>";
            echo "<tr><th>Listing ID</th><th>City</th><th>Seller ID</th><th>Agent ID</th><th>Address</th><th>Listing Price</th><th>Property Status</th><th>Square Footage</th><th>Listing Date</th></tr>";

            // echo "<br>Cannot parse the following command: " . $result . "<br>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td><td>" . $row[4] . "</td><td>" . $row[5]. "</td><td>" . $row[6] . "</td><td>" . $row[7] .  "</td><td>" . $row[8]. "</td></tr>"; //or just use "echo $row[0]"
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

            $listingid = $_POST['ListingID'];
            $status = $_POST['Status'];

            // you need the wrap the old name and new name values with single quotations
            executePlainSQL("UPDATE PropertyListing SET PropertyStatus='" . $status . "' WHERE ListingId=" . $listingid);
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
            $listing_id = $_GET['ListingID'];
            //executeBroadSQL("DELETE FROM PropertyListing WHERE ListingID = (:bind1)", $alltuples)
            executePlainSQL("DELETE FROM PropertyListing WHERE ListingID=" . $listing_id);
            OCICommit($db_conn);
        }

        function handleInsertRequest() {
            global $db_conn;

            //Getting the values from user and insert data into the table
            $tuple = array (
                ":bind1" => $_POST['ListingID'],
                ":bind2" => $_POST['CityName'],
                ":bind3" => $_POST['SellerID'],
                ":bind4" => $_POST['AgentID'],
                ":bind5" => $_POST['PropertyAddress'],
                ":bind6" => $_POST['ListingPrice'],
                ":bind7" => $_POST['PropertyStatus'],
                ":bind8" => $_POST['SquareFootage'],
                ":bind9" => $_POST['ListingDate']      
            );
            echo '<script>console.log("Your stuff here")</script>';
            
            $alltuples = array (
                $tuple
            );

            executeBoundSQL("insert into PropertyListing values (:bind1, :bind2 , :bind3, :bind4, :bind5, :bind6, :bind7, :bind8, TO_DATE(:bind9,'YYYY-MM-DD'))", $alltuples);
            OCICommit($db_conn);
        }

        function handleCountRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT Count(*) FROM PropertyListing");
            echo '<script>console.log("COUNT")</script>';


            if (($row = oci_fetch_row($result)) != false) {
                echo "<br> The number of tuples in PropertyListing: " . $row[0] . "<br>";
            }
        }

        function handleDisplayRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT * FROM PropertyListing");

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
                } else if (array_key_exists('deletePropertyRequest', $_GET)) {
                    echo '<script>console.log("DELETE req")</script>';
                    handleDeleteRequest();

                } 

                disconnectFromDB();
            }
        }

		if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
            echo '<script>console.log("POST")</script>';
            handlePOSTRequest();
        } else if (isset($_GET['countTupleRequest']) || isset($_GET['displayTupleRequest']) || isset($_GET['deletePropertyRequest'])) {
            echo '<script>console.log("GET")</script>';
            handleGETRequest();
        }
		?>
	</body>
</html>