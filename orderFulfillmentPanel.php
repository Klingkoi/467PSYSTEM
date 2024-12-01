
<?php
  include('secret.php');


  try {
    $dsn = "mysql:host=courses;dbname=".$username;
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  catch (PDOexception $e) {
    echo "Connection to database failed: ".$e->getMessage();
  } 
  
  //For some reason putting this at the top solves a bug
  if(isset($_POST["shipButton"])) {  
        $pdo->exec("UPDATE orders SET order_status = \"Shipped\" WHERE order_id = ".$_POST["shipButton"].";"); 
        header($_SERVER['PHP_SELF']); 
        header("Location: #");
        exit();
    }
?>

<html>
    <head>
    <style>
        .div1 {
            border:4px outset lightGrey;
            background-color: white;
            text-align: center;
        }
        .div2 {
            text-align: center;
        }
    </style>
        <title> Project 3A - Product System </title>
    </head>
    <body style="background-color:Turquoise;">
    <div class="div1">
        <h1 style="text-align:center;"> Warehouse Order Fulfillment </h1>
        <p>Welcome to this warehouse workstation! Process orders here.</p>
    
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

        .modal {
            position: fixed;
            inset: 0;
            background: rgba(254,126,126,0.7);
            display: none;
            align-items: center;
            justify-content: center;
        }

        .modal:target {
            display: flex;
        }

        .content {
            position: relative;
            background: white;
            padding: 10px;
        }

    </style>

    </div> 
        <div id="table_space">
            <table class="center" style="width:50%;" id="invView">
                <!-- Table headers -->
                <th colspan="7" style="background-color:White">Inventory</th>
                <tr>
                    <th style="background-color:White">Order ID</th>
                    <th style="background-color:White">Order Total (USD)</th>
                    <th style="background-color:White">Weight Total (lbs)</th>
                    <th style="background-color:White">Status</th>
                </tr>
                <!-- Table data -->
                <?php
                    $preparedOrder = $pdo->prepare("SELECT orders.order_id, orders.total_price, orders.total_weight, orders.order_status, orders.customer_id, orders.shipping_cost
                                                    FROM orders ORDER BY orders.order_id ASC");
                    $preparedOrder->execute();
                    $row = $preparedOrder->fetch(PDO::FETCH_NUM);            
                    while($row) {
                        echo "<tr>";
                            echo "<td>Order #$row[0]</td>";
                            echo "<td>$$row[1]</td>";
                            echo "<td>$row[2]</td>";
                            //Complete order button
                            if($row[3] == "Shipped" || $row[3] == "Completed") {
                                echo "<td>Shipped!</td>";
                            } else {
                                echo "<td>
                                            <div class=\"button\">
                                                <a href=\"#popup-box-$row[0]\">Complete Order $row[0]</a>
                                            </div>
                                      </td>";        
                            } 

                            //Prepare part listing with quantity
                            $preparedParts = $pdo->prepare("SELECT order_details.part_number, order_details.quantity,
                                                                parts.description, parts.price
                                                                FROM order_details 
                                                                INNER JOIN parts ON parts.number = order_details.part_number
                                                                WHERE order_details.order_id = $row[0]
                                                                ORDER BY parts.number ASC");
                            $preparedParts->execute();
                            $partRow = $preparedParts->fetch(PDO::FETCH_NUM);   
                            
                            $preparedCustomer = $pdo->prepare("SELECT name, email, address FROM customers 
                                                       WHERE customer_id = $row[4]");
                            $preparedCustomer->execute();
                            $custRow = $preparedCustomer->fetch(PDO::FETCH_NUM);   

                            //Dynamic pop-up based on order
                            echo "<div id=\"popup-box-$row[0]\" class=\"modal\">
                                    <div class=\"content\">
                                        <h1>Order $row[0]</h1>";
                            echo "<h3>Packing List/Invoice:</h3>";
                            while($partRow) {
                                $partTempTotal = $partRow[3] * $partRow[1];
                                echo "<p>$partRow[2] x $partRow[1] ($$partRow[3] x $partRow[1] = $$partTempTotal)</p>";
                                $partRow = $preparedParts->fetch(PDO::FETCH_NUM);
                            }
                            $subtotal = $row[1] - $row[5]; //total_price - shipping_cost
                            echo "<p>Subtotal: $$subtotal</p>";
                            echo "<p>Shipping: $$row[5]</p>";
                            echo "<p>Total: $$row[1]</p>";
                            echo "<h3>Shipping Confirmation:</h3>";
                            echo "<p>$custRow[0]</p>";  //name
                            echo "<p>$custRow[2]</p>";  //address
                            echo "<p>Order confirmation sent to: $custRow[1]</p>";  //email

                            //Mark as Shipped Button
                            if($row[3] != "Shipped") {
                            echo "<div class=\"button\">";
                            echo "    <form method=\"post\">";
                            echo "         <button href=\"#\" type=\"submit\" name=\"shipButton\" value=\"$row[0]\">Mark Order $row[0] as Shipped</button>";
                            echo "    </form>";
                            echo "</div>"; }

                            //X to close popup
                            echo "<a href=\"#\" style=\"position:absolute; color: #fe0606; 
                                                        top:10px; right:10px\">&times;</a>                                    
                                    </div>
                                </div>";
                            echo "</tr>";

                        $row = $preparedOrder->fetch(PDO::FETCH_NUM);  
                    }

                    
                ?>
            </table> </br> <!-- End of table -->
        <div>

        </body>
</html>