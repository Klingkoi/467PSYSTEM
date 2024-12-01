<html>
  <head>

  </head>

  <body>
    <h1> View cart </h1>
    
    <a href="./viewParts.php">View Parts</a>
    
    <?php
      // File with username and password
      include('pdo-setup.php');
      try {
        $pdo = new PDO("mysql:host=courses;dbname=$username", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      }
      catch (PDOexception $e) {
        echo "Connection to database failed: ".$e->getMessage();
      }

      $sql = "SELECT * FROM Cart";
      $result = $pdo->query($sql);
      $row = $result->fetch(PDO::FETCH_NUM);

      // TODO add delete from cart button
      echo "<table border=\"1\">";
      echo "<tr><th>Part Number (for now)</th><th>quantity</th></tr>";
      while ($row != false) {
        echo "<tr>";
        for ($i = 0; $i < sizeof($row); $i++) {
          echo "<td>".$row[$i]."</td>";
        }
        echo "</tr>";
        $row = $result->fetch(PDO::FETCH_NUM);
      }
      echo "</table>";
    ?>
    <br/>
  </body>
</html>
