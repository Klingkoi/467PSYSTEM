<?php
  include('secret.php');
  include('js.php');

  try {
    $dsn = "mysql:host=blitz.cs.niu.edu;dbname=csci467";
    $legacypdo = new PDO($dsn, "student", "student");
    $legacypdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  catch (PDOexception $e) {
    echo "Connection to database failed: ".$e->getMessage();
  } 

  try {
    $dsn = "mysql:host=courses;dbname=".$username;
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  catch (PDOexception $e) {
    echo "Connection to database failed: ".$e->getMessage();
  }
?>

<html>
    <head>
        <!-- Separating Divisions -->
    <style>
        .div1 {
            border:4px outset lightGrey;
            background-color: white;
            text-align: center;
        }
        .div2 {
            text-align: center;
        }
        .div3 {
            border:none;
            background-color:silver;
            text-align:center;
            visibility:collapse;
        }
        .content {
            position: relative;
            background: white;
            padding: 10px;
        }
        .modal {
            position: fixed;
            inset: 0;
            background: rgba(211,211,211,0.6);
            display: none;
            align-items: center;
            justify-content: center;
        }
        .modal:target {
            display: flex;
        }

    </style>
        <title> Project - Product System </title>
    </head>
    <body style="background-color:DarkSeaGreen;">
    <div class="div1">
        <h1 style="text-align:center;"> Car Parts Administration </h1>
    <!-- Show pay brackets -->
    <?php
      # Descending order
        $sql = "SELECT * FROM Brackets ORDER BY bracket_upper ASC;";
        $prepared = $pdo->prepare($sql);
        $prepared->execute();
        $row = $prepared->fetch(PDO::FETCH_NUM);
    ?>
        <!-- TABLE -->
    <style>
    table, th {
        border:3px solid black; 
        border-collapse: collapse;
        background-color:white;
        width:75%;
    }
    td {
        border:3px solid black; 
        border-collapse: collapse;
        background-color:white;
        width:25%;
    }
    table.center {
        margin-left: auto; 
        margin-right: auto;
    }
    button {
        background-color:tomato;
        border:none;
        color:black;
        padding:4px 24px;
        text-align:center;
        font-size:14px;
        transition-duration: 0.3s;
    }
    button:hover {
        background-color:orange;
        color:white;
    }

    </style>

    <!-- This button swaps views between the orders and brackets -->
    <button onclick="toggleDisplay()">Swap Views</button>
    <p></p>
    <!-- Brackets Table View -->
    </div> </br >

    <div class="div2" id="bracketView">
    <table class="center" style="width:50%;" id="bracketTable">
        <!-- Table headers -->
        <th colspan="7" style="background-color:White">Weight Brackets</th>
        <tr>
            <th style="background-color:White">Bracket</th><th style="background-color:White">Cost</th>
        </tr>
        <!-- Table data -->
        <?php
            $prevRow = $row;
            $row = $prepared->fetch(PDO::FETCH_NUM);
            while($row) {
                echo "<tr>";
                    echo "<td>From $prevRow[0] to $row[0] lbs</td>";
                    echo "<td>$ $prevRow[1]</td>";
                echo "</tr>";
                $prevRow = $row;
                $row = $prepared->fetch(PDO::FETCH_NUM);
            }
            echo "<tr>";
                $prepared = $pdo->prepare("SELECT * FROM Brackets ORDER BY bracket_upper DESC LIMIT 1");
                $prepared->execute();
                $row = $prepared->fetch(PDO::FETCH_NUM);
                echo "<td>Over $row[0] lbs</td>";
                echo "<td>$ $row[1]</td>";
            echo "</tr>";
        ?>
    </table> </br> 
    </div>
    <!-- End of table -->

    <!-- Brackets DB Editting-->
        <div class="div2" id="Qs">
            <form action="" method="post">
                <label for="bracket">Put a new bracket limiter: </label>
                <input type="number" id="bracket" name="bracket" min="0"/>
                <label for"cost"> Provide a cost for the new bracket: </label>
                <input type="number" id="cost" name="cost" min="0" step=".01"/>
                <input type="submit" id="submitted" name="submitted" value ="Add"/>
            </form>
            <form action="" method="post">
                <label for="bracket">Pick a bracket limiter to remove: </label>
                <input type="number" id="rBracket" name="rBracket" min="1"/>
                <input type="submit" id="submitted" name="submitted" value ="Remove"/>
            </form>
        </div>

    <!-- Orders Table View-->
    
    <!-- Sort by Options -->
        <div class="div3" id="SortBy">
            <form action="" id="sortingForm" method="get">
                <p>Adjust search: </p>
                    <!-- // Date Search -->
                <label for="dateStart">Dates from: </label>
                <input type="date" id="dateStart" name="dateStart" value="2000-01-01"/>
                <label for="dateEnd"> to: </label>
                <input type="date" id="dateEnd" name="dateEnd" value="2025-01-01"/>
                    <!-- // Price Search -->
                <label for="priceStart"> Totals from: </label>
                <input type="number" id="priceStart" name="priceStart" value="0" min="0" max="99998"/>
                <label for="priceEnd"> to: </label>
                <input type="number" id="priceEnd" name="priceEnd" value="99999" min="0" max="99999"/>
                    <!-- Order Status Search -->
                <label for="status"> Status:  </label>
                <select type="text" id="status" name="status" value="All">
                    <option value="All">All</option>
                    <option value="Authorized">Authorized</option>
                    <option value="Packed">Packed</option>
                    <option value="Shipped">Shipped</option>
                    <option value="Completed">Completed</option>
                </select>
                <input type="submit" id="submitted" name="submitted" value ="Search"/>
            </form>
        </div>
        <div class="div3" id="Orders">
            <?php
                if(isset($_GET["dateStart"])) {
                    // Used for seeing which orders are shown
                    $dStart = $_GET["dateStart"];
                    $dEnd = $_GET["dateEnd"];
                    $pStart = $_GET["priceStart"];
                    $pEnd = $_GET["priceEnd"];
                    $status = $_GET["status"];
                    if($status == "All") { // Need because all is not a status, but instead refers to any status
                        $sql = "SELECT order_id, total_price, total_weight, order_status, customer_id,shipping_cost, order_date FROM orders WHERE order_date >= '$dStart' AND order_date <= '$dEnd' AND total_price >= $pStart AND total_price <= $pEnd ORDER BY order_id ASC";
                        $nSQL = "SELECT count(*) FROM orders WHERE order_date >= '$dStart' AND order_date <= '$dEnd' AND total_price >= $pStart AND total_price <= $pEnd";
                    }
                    else { // All orders with any number of modifications
                        $sql = "SELECT order_id, total_price, total_weight, order_status, customer_id,shipping_cost, order_date FROM orders WHERE order_date >= '$dStart' AND order_date <= '$dEnd' AND total_price >= $pStart AND total_price <= $pEnd AND order_status = '$status' ORDER BY order_id ASC";
                        $nSQL = "SELECT count(*) FROM orders WHERE order_date >= '$dStart' AND order_date <= '$dEnd' AND total_price >= $pStart AND total_price <= $pEnd AND order_status = '$status'";
                    }
                    // Making the table
                    $prepared = $pdo->prepare($sql);
                    $prepared->execute();
                    $row = $prepared->fetch(PDO::FETCH_NUM);
                    echo "<table>";
                    while($row) {
                        echo "<tr>";
                            echo "<td>Order Number: $row[0]($row[3]) </td>";
                            echo "<td> $row[6]</td>";
                            echo "<td>Total(w/o Shipping): $row[1] </td>";
                            echo "<td> 
                                    <div class=\"button\">
                                        <a href=\"#popup-box-$row[0]\">Order $row[0] Details</a>
                                    </div>
                                  </td>";

                            //Prepare part listing with quantity
                            $orderPrepared = $pdo->prepare("SELECT * FROM order_details ORDER BY part_number ASC");
                            $legacyprepared = $legacypdo->prepare("SELECT * FROM parts ORDER BY number ASC");
            
                            $orderPrepared->execute();
                            $inventory = $orderPrepared->fetchAll(PDO::FETCH_ASSOC);      

                            $legacyprepared->execute();
                            $parts = $legacyprepared->fetchAll(PDO::FETCH_ASSOC);   
                            
                            //Join Tables with arrays since can't join tables from 2 different PDOs
                            $joinedDetailsAndInventory = [];
                            foreach($order_detail as $entry)
                            {
                                foreach ($parts as $part) {
                                    //if order_details.part_number == parts.number
                                    if($entry["part_number"] == $part["number"] && $entry["order_id"] == $row[0])
                                        $joinedDetailsAndInventory[] = array_merge($entry, $part);
                                }
                            }
                            
                            $preparedCustomer = $legacypdo->prepare("SELECT name, street, city, contact FROM customers WHERE id = $row[4]");
                            $preparedCustomer->execute();
                            $custRow = $preparedCustomer->fetch(PDO::FETCH_NUM);   

                            //Dynamic pop-up based on order
                            echo "<div id=\"popup-box-$row[0]\" class=\"modal\">
                                    <div class=\"content\">
                                        <h1>Order $row[0]</h1>";
                            echo "<a href=\"#\" style=\"position:absolute; color: #fe0606; 
                                                        top:10px; right:10px\">&times;</a>";
                            echo "<h3>Packing List/Invoice:</h3>";
                            foreach($joinedDetailsAndInventory as $partRow) {
                                $partTempTotal = $partRow["price"] * $partRow["quantity"];
                                echo '<p>$partRow["description"] x $partRow["quantity"] ($$partRow["price"] x $partRow["quantity"] = $$partTempTotal)</p>';
                            }
                            $wPrepare = $pdo->prepare("SELECT bracket_upper, cost FROM Brackets");
                            $wPrepare->execute();
                            $weight = $wPrepare->fetch(PDO::FETCH_NUM);
                            while($weight) {
                                if($row[2] >= $weight[0]) {
                                    $shippingCost = $weight[1];
                                }
                                else {
                                    break;
                                }
                                $weight = $wPrepare->fetch(PDO::FETCH_NUM);
                            }
                            $subtotal = $row[1];
                            echo "<p>Subtotal: $$subtotal</p>";
                            echo "<p>Shipping: $$shippingCost</p>";
                            $total = $row[1] + $shippingCost;
                            echo "<p>Total: $$total</p>";
                            echo "<h3>Shipping Confirmation:</h3>";
                            echo "<p>$custRow[0]</p>";  //name
                            echo "<p>$custRow[1]</p>";  //street
                            echo "<p>$custRow[2]</p>";  //city
                            echo "<p>Order confirmation sent to: $custRow[3]</p>";  //email
                            //X to close popup
                            echo "</div>
                                </div>";
                            echo "</tr>";
                        $row = $prepared->fetch(PDO::FETCH_NUM);
                        }
                    }
                ?>
                </table> </br >

                <?php
                $nPrepared = $pdo->prepare($nSQL);
                $nPrepared->execute();
                $nRows = $nPrepared->fetch(PDO::FETCH_NUM);
                echo "<p style=\"text-align:left;\" >$nRows[0] order(s) returned!</p>";
                echo "</br >";
                ?>
            </div>
            <?php
                // Add a new bracket limiter
                if(isset($_POST["bracket"]) && isset($_POST["cost"])) {
                    $pdo->exec("INSERT INTO Brackets VALUES(".$_POST["bracket"].", ".$_POST["cost"].");");
                }
                // Remove a bracket limiter
                else if(isset($_POST["rBracket"])) {
                    $pdo->exec("DELETE FROM Brackets WHERE bracket_upper = ".$_POST["rBracket"].";");
                }
            ?>
        </body>
</html>
