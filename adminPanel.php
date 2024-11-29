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
?>

<html>
    <head>
        <title> Project - Product System </title>
    </head>
    <body style="background-color:DarkSeaGreen;">
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
    table, th, td{
        border:3px solid black; 
        border-collapse: collapse;
        background-color:white;
    }
    </style>
    <table style="width:50% ">
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
                    echo "<td>From $prevRow[0] to $row[0]</td>";
                    echo "<td>$ $prevRow[1]</td>";
                echo "</tr>";
                $prevRow = $row;
                $row = $prepared->fetch(PDO::FETCH_NUM);
            }
            echo "<tr>";
                $prepared = $pdo->prepare("SELECT * FROM Brackets ORDER BY bracket_upper DESC LIMIT 1");
                $prepared->execute();
                $row = $prepared->fetch(PDO::FETCH_NUM);
                echo "<td>Over $row[0]</td>";
                echo "<td>$ $row[1]</td>";
            echo "</tr>";
        ?>
        </table> </br> <!-- End of table -->
            <form action="" method="post">
                <label for="bracket">Put a new bracket limiter: </label>
                <input type="number" id="bracket" name="bracket" min="0"/>
                <label for"cost"> Provide a cost for the new bracket: </label>
                <input type="number" id="cost" name="cost" min="0" step=".01"/>
                <input type="submit" id="submitted" name="submitted" value ="Enter"/>
            </form>
            <form action="" method="post">
                <label for="bracket">Pick a bracket limiter to remove: </label>
                <input type="number" id="rBracket" name="rBracket" min="1"/>
                <input type="submit" id="submitted" name="submitted" value ="Enter"/>
            </form>
            <?php
            if(isset($_POST["bracket"]) && isset($_POST["cost"])) {
                $pdo->exec("INSERT INTO Brackets VALUES(".$_POST["bracket"].", ".$_POST["cost"].");");
                }
            else if(isset($_POST["rBracket"])) {
                $pdo->exec("DELETE FROM Brackets WHERE bracket_upper = ".$_POST["rBracket"].";");
                }
            ?>
        </body>
</html>
