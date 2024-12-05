<?php
  include('secret.php');

  try {
    $dsn = "mysql:host=blitz.cs.niu.edu;dbname=csci467";
    $legacypdo = new PDO($dsn, $legacyUsername, $legacyPassword);
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
        <h1 style="text-align:center;"> Car Parts Receiving Desk </h1>
        <p>Welcome, Inventory Manager! Update part quantity here.</p>
    
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

    </div> 
        <div class="div2" id="Qs">
            <form action="" method="post">
                <label for="part">Pick an part to update the quantity of: </label>
                <!-- Create Dropdown of parts to update -->
                <select name="dropdown" id="dropdown">
                    <?php
                        $prepared = $legacypdo->prepare("SELECT number,description FROM parts ORDER BY number ASC");
                        $prepared->execute();
                        $row = $prepared->fetch(PDO::FETCH_NUM);            
                        while($row) {
                            echo "<option value=\"$row[0]\">Part $row[0]: $row[1]</option>";
                            $row = $prepared->fetch(PDO::FETCH_NUM);  
                        }
                    ?>
                </select> <br>
                <label for="new_quantity">Enter the new quantity: </label>
                <input type="number" id="new_quantity" name="quantity" min="0" value="0" step="1" required/>
                <br>
                <input type="submit" id="submitted" name="submitted" value ="Update Part"/>
            </form>
            

        </div>

        <?php
            if(isset($_POST["dropdown"]) && isset($_POST["quantity"])) {
                $pdo->exec("UPDATE inventory SET quantity_on_hand = ".$_POST["quantity"]." WHERE part_number = ".$_POST["dropdown"].";");
            }
        ?>

        <div id="table_space">
            <table class="center" style="width:50%;" id="invView">
                <!-- Table headers -->
                <th colspan="7" style="background-color:White">Inventory</th>
                <tr>
                    <th style="background-color:White; width:5%;">Inventory ID</th>
                    <th style="background-color:White; width:5%;">Part Number</th>
                    <th style="background-color:White">Description</th>
                    <th style="background-color:White">Price (USD)</th>
                    <th style="background-color:White">Weight (lbs)</th>
                    <th style="background-color:White">Image</th>
                    <th style="background-color:White;bold">Quantity On Hand</th>
                </tr>
                <!-- Table data -->
                <?php
                    //$prepared = $pdo->prepare("SELECT * FROM inventory INNER JOIN parts ON inventory.part_number = parts.number ORDER BY part_number ASC");
                    $prepared = $pdo->prepare("SELECT * FROM inventory ORDER BY part_number ASC");
                    $legacyprepared = $legacypdo->prepare("SELECT * FROM parts ORDER BY number ASC");
    
                    $prepared->execute();
                    $inventory = $prepared->fetchAll(PDO::FETCH_ASSOC);      

                    $legacyprepared->execute();
                    $parts = $legacyprepared->fetchAll(PDO::FETCH_ASSOC);   
                    
                    //Join Tables with arrays since can't join tables from 2 different PDOs
                    $joinedPartsAndInventory = [];
                    foreach($inventory as $entry)
                    {
                        foreach ($parts as $part) {
                            //if inventory.part_number == parts.number
                            if($entry["part_number"] == $part["number"])
                                $joinedPartsAndInventory[] = array_merge($entry, $part);
                        }
                    }

                    //add rows
                    foreach($joinedPartsAndInventory as $row) {
                        echo "<tr>";
                            echo "<td>".$row["inventory_id"]."</td>";
                            echo "<td>".$row["part_number"]."</td>";
                            echo "<td>".$row["description"]."</td>";
                            echo "<td>".$row["price"]."</td>";
                            echo "<td>".$row["weight"]."</td>";
                            echo "<td><img src=\"".$row["pictureURL"]."\" width=\"100\"></td>"; 
                            echo "<td style=\"font-weight: bold\">".$row["quantity_on_hand"]."</td>";
                        echo "</tr>"; 
                    }
                ?>
            </table> </br> <!-- End of table -->
        <div>

        </body>
</html>