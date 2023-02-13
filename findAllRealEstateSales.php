
  <html>
    <head>
        <title>CPSC 304 PHP/Oracle Demonstration</title>
    </head>

    <body>
        <h2>Get Real Estate Agents Involved in These Three Purchases</h2>
        <form method="GET" action= "findAllRealEstateSales.php"> <!--refresh page when submitted-->
            <input type="hidden" id="getSalesRequest" name="getSalesRequest"> 
            Purchase ID 1: <input type="text" name="PurchaseID1"> <br /><br />
            Purchase ID 2: <input type="text" name="PurchaseID2"> <br /><br />
            Purchase ID 3: <input type="text" name="PurchaseID3"> <br /><br />                   
            <input type="submit" value="Get Real Estate Agents" name="getSalesRequest"></p>
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
            echo "<br>Real Estate Agent:<br>";
            echo "<table>";
            echo "<tr><th>Agent ID</th><th>Name</th><th>Phone</th><th>Email</th><th>Rate</th><th>Brokerage</th></tr>";

            // echo "<br>Cannot parse the following command: " . $result . "<br>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td><td>" . $row[4] . "</td><td>" . $row[5] . "</td></tr>"; //or just use "echo $row[0]"
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
            OCICommit($db_conn);
        }

        function handleResetRequest() {
            global $db_conn;
            // Drop old table
            
            OCICommit($db_conn);
        }

        function handleSalesRequest() {
            global $db_conn;

            //Getting the values from user and insert data into the table
            $pid_1 = $_GET['PurchaseID1'];
            $pid_2 = $_GET['PurchaseID2'];
            $pid_3 = $_GET['PurchaseID3'];


            $result = executePlainSQL("select distinct *
            from RealEstateAgent a, (select distinct r.AgentID from PurchaseWithRealEstateAgent r
            where not exists (select p.PurchaseID from Purchase p where p.PurchaseID = " . $pid_1 . " or p.PurchaseID = " . $pid_2 . " or p.PurchaseID = " . $pid_3 . "
            minus
            select ra.PurchaseID from PurchaseWithRealEstateAgent ra where ra.AgentID = r.AgentID)) b
            where a.AgentID = b.AgentID
            ");

            


            // $result = executePlainSQL("SELECT * FROM RealEstateAgent");
            printResult($result);
        }

        function handleInsertRequest() {
            global $db_conn;
        }

        function handleCountRequest() {
            global $db_conn;
        }

        function handleDisplayRequest() {
            global $db_conn;
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
                } else if (array_key_exists('getSalesRequest', $_GET)) {
                    echo '<script>console.log("GET SALES req")</script>';
                    handleSalesRequest();

                } 

                disconnectFromDB();
            }
        }

		if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
            echo '<script>console.log("POST")</script>';
            handlePOSTRequest();
        } else if (isset($_GET['countTupleRequest']) || isset($_GET['displayTupleRequest']) || isset($_GET['getSalesRequest'])) {
            echo '<script>console.log("GET")</script>';
            handleGETRequest();
        }
		?>
	</body>
</html>