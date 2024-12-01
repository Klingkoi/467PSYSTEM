<?php
  include('secret.php');
  include('js.php');

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
            text-align:left;
            visibility:collapse;
        }
        .div4 {
            text-align: center;
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

    <div class="div4" id="bracketView">
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
        <form action="" method="post">
                <p style="text-align:center;">Adjust search: </p>
                <!-- // Date Search -->
                <label for="dateStart">Dates from: </label>
                <input type="date" id="dateStart" name="dateStart" value="2000-01-01"/>
                <label for="dateEnd"> to: </label>
                <input type="date" id="dateEnd" name="dateEnd" value="2025-01-01"/>
                <!-- // Price Search -->
                <label for="priceStart"> Totals from: </label>
                <input type="number" id="priceStart" name="priceStart" value="0" min="0"/>
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
