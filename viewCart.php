<html>
  <head>
    <title>Group 3A: viewing cart</title>
  </head>
  <body>
    <h1> View cart </h1>
 
    <a href="./viewParts.php">View Parts</a><br>
    
    <?php
      // ==============================
      // === Connect with databases ===
      // ==============================
      // File with username and password
      include('secret.php');

      // Establish connection to the legacy database
      try {
        $pdo = new PDO("mysql:host=blitz.cs.niu.edu;dbname=csci467", "student", "student");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      }
      catch (PDOexception $e) {
        echo "Connection to database failed: ".$e->getMessage();
      }
      // Establish connection to our database
      try {
        $pdo2 = new PDO("mysql:host=courses;dbname=$username", $username, $password);
        $pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      }
      catch (PDOexception $e) {
        echo "Connection to database failed: ".$e->getMessage();
      }

      // ===========================
      // === Handle deleting row ===
      // ===========================
      $result = $pdo->query("SELECT * FROM parts");
      $row = $result->fetch(PDO::FETCH_NUM);

      // Match with every part present in current cart 
      for ($i = 0; $row != false; $i++) {
        if(isset($_POST[$i])) {
          $sql = "DELETE FROM Cart WHERE part_ID = ".$i;
          $query = $pdo2->query($sql);
        }
        $row = $result->fetch(PDO::FETCH_NUM);
      } 

      // ==========================
      // === Print current cart ===
      // ==========================
      // Grab all parts
      $sql = "SELECT * FROM parts";
      $result = $pdo->query($sql);
      $row = $result->fetch(PDO::FETCH_NUM);

      // Grab all parts in cart
      $sql = "SELECT * FROM Cart";
      $result2 = $pdo2->query($sql);
      $num_parts = $result2->rowCount(); // Number of parts in cart
      $row2 = $result2->fetch(PDO::FETCH_NUM);

      // Set-up
      $sql = "SELECT * FROM parts";
      $total_price = 0;
      $total_weight = 0;
      echo "There are ".$num_parts." items in your cart";

      // Table Header
      echo "<table border=\"1\">";
      echo "<tr><th>Picture</th><th>Part</th><th>Price</th><th>Weight</th><th>Quantity</th></tr>";
      // Table rows
      while ($row2 != false) {
        while ($row != false) {
          if ($row[0] == $row2[0]) {
            // Print columns
            echo "<tr>";
            echo "<td><img src=\"".$row[4]."\" alt=\"pic\"/></td>"; // Image
            echo "<td>".$row[1]."</td>"; // Part name
            echo "<td>$".$row[2]."</td>"; // Part price
            echo "<td>".$row[3]."lbs.</td>"; // Part weight
            echo "<td>".$row2[1]."</td>"; // Quantity in cart
            echo "<td><form action=\"\" method=\"POST\"><input type=\"submit\" name=\"".$row[0]."\" value=\"Remove\"/></form></td>";
            echo "</tr>";
            // Add details to totals
            $total_price += $row[2] * $row2[1];
            $total_weight += $row[3] * $row2[1];
          }
          $row = $result->fetch(PDO::FETCH_NUM);
        }
        $row2 = $result2->fetch(PDO::FETCH_NUM);
        $result = $pdo->query($sql);
        $row = $result->fetch(PDO::FETCH_NUM);
      }
      echo "</table>";

      // =================================
      // === Print billing information ===
      // =================================
      echo "Total price: $".$total_price;
      echo "<br>";
      echo "Total weight: ".$total_weight."lbs";
      echo "<br>";
      $shipping_cost = 0;
      $result2 = $pdo2->query("SELECT * FROM Brackets"); 
      $row2 = $result2->fetch(PDO::FETCH_NUM);
      $bound = $row2[0];
      $row2 = $result2->fetch(PDO::FETCH_NUM);
      while ($row2 != false) {
        // If previous 
        if ($bound < $total_weight) {
          $shipping_cost = $row2[1];
          break;
        }
        $bound = $row2[0];
        $row2 = $result2->fetch(PDO::FETCH_NUM);
      }
      $shipping_cost += 1 - 1;
      echo "Shipping costs: $".$shipping_cost;
      echo "<br>";
      echo "Super total: $".($total_price + $shipping_cost);
      echo "<br>";

      $vi_id = 00200;
      $trans_id = 907987654321297;
      // ==========================
      // === Credit card system ===
      // ==========================
      $url = 'http://blitz.cs.niu.edu/CreditCard/';
      $data = array(
	  'vendor' => 'VE'.rand(0,9).rand(0,9).rand(0,9).'-'.rand(0,9).rand(0,9),
	  'trans' => rand(0,9).rand(0,9).rand(0,9).'-987654321-'.rand(0,9).rand(0,9).rand(0,9),
	  'cc' => '',
	  'name' => '', 
	  'exp' => '', 
	  'amount' => (string)($total_price + $shipping_cost));

      echo "<br><form action=\"\" method=\"POST\">Name: <input type=\"text\" name=\"name\"/><br>";
      echo "Email: <input type=\"text\" name=\"email\"/><br>";
      echo "Address: <input type=\"text\" name=\"address\"/><br>";
      echo "CC: <input type=\"text\" name=\"cc\"/><br>";
      echo "Exp: <input type=\"text\" name=\"exp\"/><br>";
      echo "<input type=\"submit\" name=\"submit\" value=\"Purchase\"/></form>";

      $data['cc'] = $_POST['cc'];
      $data['name'] = $_POST['name'];
      $data['exp'] = $_POST['exp'];

      // If price is higher than upper_bound of previous row 
      if ($total_price > 0 && $_POST['name'] != '' && $_POST['cc'] != '' && $num_parts > 0) {
        $options = array(
          'http' => array(
          'header' => array('Content-type: application/json', 'Accept: application/json'),
          'method' => 'POST',
          'content'=> json_encode($data)
          )
        ); 

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        echo "<br>Order placed!";
        
        // Create the customer
        $sql = "INSERT INTO customers (name, email, address) VALUES (?, ?, ?)";
        $prepared = $pdo2->prepare($sql);
        $prepared->execute(array($_POST['name'], $_POST['email'], $_POST['address']));

        // Find customer_id
        $sql = "SELECT * FROM customers ORDER BY customer_id DESC LIMIT 1";
        $result2 = $pdo2->query($sql);
        $row2 = $result2->fetch(PDO::FETCH_NUM);
        $customer_id = $row2[0];
        // Create the order
        $sql = "INSERT INTO orders (customer_id, total_price, total_weight, shipping_cost, order_status) VALUES (".$customer_id.", ".$total_price.", ".$total_weight.", ".$shipping_cost.", \"authorized\")";
        $pdo2->query($sql);

        // Find order_id
        $sql = "SELECT * FROM orders ORDER BY order_id DESC LIMIT 1";
        $result2 = $pdo2->query($sql);
        $row2 = $result2->fetch(PDO::FETCH_NUM);
        $order_id = $row2[0];

        // Create the order details for the order
        $result2 = $pdo2->query("SELECT * FROM Cart");
        $row2 = $result2->fetch(PDO::FETCH_NUM);
        while ($row2 != false) {
          $sql = "INSERT INTO order_details (order_id, part_number, quantity) VALUES (".$order_id.", ".$row2[0].", ".$row2[1].")";
          $pdo2->exec($sql);
          // Delete cart item from table
          $sql = "DELETE FROM Cart WHERE part_ID = ".$row2[0];
          $pdo2->exec($sql);
          $row2 = $result2->fetch(PDO::FETCH_NUM);
        }
      }
    ?>
    <br/>
  </body>
</html>
