<html>
  <!-- Aaron Carreon (z1957830) -->
  <!-- Group 3A -->
  <head>
    <title> Aaron Carreon (z1957830) </title>
  </head>

  <body>
    <h1> View parts </h1>
    
    <a href="./viewCart.php">View Cart</a>
    <br/>
    <?php
      // File with username and password
      include('pdo-setup.php');

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

      // TODO add search feature
      $sql = "SELECT number, pictureURL, description, price, weight FROM parts";
      $result = $pdo->query($sql);
      $row = $result->fetch(PDO::FETCH_NUM);

      $sql = "SELECT * FROM Inventory";
      $result2 = $pdo2->query($sql);
      $row2 = $result2->fetch(PDO::FETCH_NUM);

      // Print table
      echo "<table border=\"1\">";
      echo "<tr>Part List</tr>";
      echo "<tr><th>Image</th><th>Part</th><th>Price</th><th>Weight</th><th>Stock</th><th>How much?</th></tr>";
      while ($row != false) {
        while ($row2 != false) {
          // If part number matches, print the row
          if ($row[0] == $row2[0]) {
            // Print out all part columns
            echo "<tr>";
            for ($i = 1; $i < sizeof($row); $i++) {
              if ($i == 1) {
                //Print image
                echo "<td><img src=\"".$row[$i]."\" alt=\"test\"/></td>";
              }
              else {
                echo "<td>".$row[$i]."</td>";
              }
            }
            // Print out stock column
            echo "<td>".$row2[1]."</td>";
            // Print out select column and add to cart button
            echo "<td>";
            echo "<form action=\"\" method=\"POST\">";
            echo "<select id=\"".$row[0]."\" name=\"".$row[0]."\">";
            for ($i = 0; $i <= $row2[1]; $i++) {
              echo "<option value=\"".$i."\">".$i."</option>";
            }
            echo "</select>";
            if ($row2[1] != 0) {
              echo "<input type=\"submit\" name=\"".$row[0]."-sub\" value=\"Add to cart\">";
            }
            echo "</form>";
            echo "</td>";
            echo "</tr>";
          }
          $row2 = $result2->fetch(PDO::FETCH_NUM);
        }
        $row = $result->fetch(PDO::FETCH_NUM);
        $result2 = $pdo2->query($sql);
        $row2 = $result2->fetch(PDO::FETCH_NUM);
      }
      echo "</table>";

      // Insert into cart
      // TODO add overwrite feature for cart
      for ($i = 1; $i < 200; $i++) {
        if (isset($_POST[$i])) {
          echo "Part number: ".$i." quantity chosen: ".$_POST[$i];
          $sql = "INSERT INTO Cart VALUES (?, ?)";
          $prepared = $pdo2->prepare($sql);
          $prepared->execute(array($i, $_POST[$i]));
        }
      } 
    ?>
  </body>
</html>
