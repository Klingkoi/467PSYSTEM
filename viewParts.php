<html>
  <!-- Aaron Carreon (z1957830) -->
  <!-- Group 3A -->
  <head>
    <title> Aaron Carreon (z1957830) </title>
  </head>

  <body>
    <h1> View parts </h1>
    
    <a href="./viewCart.php">View Cart</a>

    <form action="" method="POST">
      <label for="search">Please search for a part:</label>
      <input type="text" name="search"/> <br>
    <?php
      // File with username and password
      include('secret.php');

      // Connect to legacy database
      try {
        $pdo = new PDO("mysql:host=blitz.cs.niu.edu;dbname=csci467", "student", "student");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      }
      catch (PDOexception $e) {
        echo "Connection to database failed: ".$e->getMessage();
      }
      // Connect to our database
      try {
        $pdo2 = new PDO("mysql:host=courses;dbname=$username", $username, $password);
        $pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      }
      catch (PDOexception $e) {
        echo "Connection to database 2 failed: ".$e->getMessage();
      }
      
      // Searching implementation
      $search = $_POST["search"];

      $sql = "SELECT number, pictureURL, description, price, weight FROM csci467.parts WHERE description LIKE \"%".$search."%\"";
      $result = $pdo->query($sql);
      $row = $result->fetch(PDO::FETCH_NUM);

      $sql = "SELECT * FROM inventory";
      $result2 = $pdo2->query($sql);
      $row2 = $result2->fetch(PDO::FETCH_NUM);

      // Print table
      echo "<table border=\"1\">";
      echo "<tr>Part List</tr>";
      echo "<tr><th>Image</th><th>Part</th><th>Price</th><th>Weight</th><th>Stock</th></tr>";
      while ($row != false) {
        // Print out all part columns
        echo "<tr>";
        for ($i = 1; $i < sizeof($row); $i++) {
          echo "<td>";
          if ($i == 1) {
            //Print image
            echo "<img src=\"".$row[$i]."\" alt=\"pic\"/>";
          }
          elseif ($i == 3) {
            echo "$".$row[$i];
          }
          elseif ($i == 4) {
            echo $row[$i]."lbs.";
          }
          else {
            echo $row[$i];
          }
          echo "</td>";
        }
        // Print the stock column
        // Find if inventory contains the part, if so then print it, if not then put 0
        $result2 = $pdo2->query("SELECT * FROM inventory WHERE part_number = \"".$row[0]."\"");
        $row2 = $result2->fetch(PDO::FETCH_NUM);
        // No inventory for that part
        if ($row2 == false) {
          echo "<td>0</td>";
        }
        // There is inventory for that part
        else {
          echo "<td>".$row2[2];
          echo "</td><td><form action=\"\" method=\"POST\">";
          echo "<select id=\"".$row[0]."\" name=\"".$row[0]."\">";
          for ($i = 1; $i <= $row2[2]; $i++) {
            echo "<option value=\"".$i."\">".$i."</option>";
          }
          echo "</select>";
          echo "<input type=\"submit\" name=\"".$row[0]."-sub\" value=\"Add to cart\"/>";
          echo "</form>";
          echo "</td>";
        }
        echo "</tr>";
        $row = $result->fetch(PDO::FETCH_NUM);
      }
      echo "</table>";

      // Insert into cart
      for ($i = 1; $i < 200; $i++) {
        if (isset($_POST[$i])) {
          $sql = "REPLACE INTO Cart VALUES (?, ?)";
          $prepared = $pdo2->prepare($sql);
          $prepared->execute(array($i, $_POST[$i]));
        }
      } 
    ?>
    </form>
  </body>
</html>
