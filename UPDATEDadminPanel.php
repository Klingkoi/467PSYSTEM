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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Parts Receiving Desk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0f7fa;
            margin: 0;
            padding: 0;
        }

        .div1, .div2 {
            text-align: center;
            margin: 20px auto;
            padding: 20px;
            max-width: 800px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        h1 {
            color: #00796b;
        }

        p {
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        table th {
            background-color: #00796b;
            color: white;
            cursor: pointer;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        button {
            background-color: tomato;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: orange;
        }

        /* Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .modal-content h3 {
            margin-top: 0;
        }

        .close-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .close-btn:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="div1">
        <h1>Car Parts Receiving Desk</h1>
        <p>Welcome, Inventory Manager! Update part quantities here.</p>
    </div>

    <div class="div2">
        <form id="updateForm" method="post">
            <label for="dropdown">Pick a part to update:</label>
            <select name="dropdown" id="dropdown" required>
                <?php
                $prepared = $pdo->prepare("SELECT number, description FROM parts ORDER BY number ASC");
                $prepared->execute();
                while ($row = $prepared->fetch(PDO::FETCH_NUM)) {
                    echo "<option value=\"$row[0]\">Part $row[0]: $row[1]</option>";
                }
                ?>
            </select><br><br>

            <label for="new_quantity">Enter the new quantity:</label>
            <input type="number" id="new_quantity" name="quantity" min="0" step="1" required><br><br>

            <button type="submit">Update Part</button>
        </form>
    </div>

    <div class="div2" id="table_space">
        <table id="invView">
            <thead>
                <tr>
                    <th>Inventory ID</th>
                    <th>Part Number</th>
                    <th>Description</th>
                    <th>Price (USD)</th>
                    <th>Weight (lbs)</th>
                    <th>Image</th>
                    <th>Quantity On Hand</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $prepared = $pdo->prepare("SELECT * FROM inventory INNER JOIN parts ON inventory.part_number = parts.number ORDER BY part_number ASC");
                $prepared->execute();
                while ($row = $prepared->fetch(PDO::FETCH_NUM)) {
                    echo "<tr>";
                    echo "<td>$row[0]</td>";
                    echo "<td>$row[1]</td>";
                    echo "<td>$row[4]</td>";
                    echo "<td>$$row[5]</td>";
                    echo "<td>$row[6]</td>";
                    echo "<td><img src=\"$row[7]\" width=\"50\"></td>";
                    echo "<td>$row[2]</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <h3>Inventory Updated Successfully!</h3>
            <button class="close-btn" onclick="closeModal()">Close</button>
        </div>
    </div>

    <script>
        // Success Notification
        function showSuccess(message) {
            const successDiv = document.createElement('div');
            successDiv.textContent = message;
            successDiv.style.position = 'fixed';
            successDiv.style.bottom = '20px';
            successDiv.style.right = '20px';
            successDiv.style.padding = '10px 20px';
            successDiv.style.backgroundColor = '#4CAF50';
            successDiv.style.color = 'white';
            successDiv.style.borderRadius = '5px';
            document.body.appendChild(successDiv);

            setTimeout(() => {
                successDiv.remove();
            }, 3000);
        }

        // Handle form submission and show success modal
        document.getElementById("updateForm").addEventListener("submit", function(event) {
            event.preventDefault(); // Prevent default form submission
            showSuccess("Inventory updated successfully!");
        });

        // Table Sorting
        document.querySelectorAll("#invView th").forEach((header, index) => {
            header.addEventListener("click", () => sortTable(index));
        });

        function sortTable(columnIndex) {
            const table = document.querySelector("#invView");
            const rows = Array.from(table.rows).slice(1);
            const isAscending = header.dataset.order === "asc";

            rows.sort((a, b) => {
                const aText = a.cells[columnIndex].textContent.trim();
                const bText = b.cells[columnIndex].textContent.trim();
                return isAscending
                    ? aText.localeCompare(bText)
                    : bText.localeCompare(aText);
            });

            rows.forEach(row => table.appendChild(row));
            header.dataset.order = isAscending ? "desc" : "asc";
        }

        // Row Hover Effects
        document.querySelectorAll("#invView tbody tr").forEach(row => {
            row.addEventListener("mouseover", () => {
                row.style.backgroundColor = "#f1f1f1";
            });
            row.addEventListener("mouseout", () => {
                row.style.backgroundColor = "";
            });
        });

        // Modal
        function closeModal() {
            document.getElementById("successModal").classList.remove("active");
        }
    </script>
</body>
</html>
