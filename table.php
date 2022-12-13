<!DOCTYPE html>
<html>
    <head>
        <title>Used Car Price Generator</title>
        <link rel="stylesheet" href="styles.css">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="table.php">Prices Generated</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
    </head>
    
    <body>
        <h1>Used Car Price Generator: Prices Generated</h1>
        <br>
        <h3>Below is a table of the last 10 prices generated.<h3>
        <br>
        <table align="center" border="1">
        <?php
            //Create connection to the mysql database
            $cnx = new mysqli('localhost', 'username', 'password', 'usedcars');

            if ($cnx->connect_error)
                die('Connection failed: ' . $cnx->connect_error);

            //Query for the last 10 prices predicted
            $query = 'SELECT * FROM (
                        SELECT * FROM predictions ORDER BY v_id DESC LIMIT 10
                        )Var1 ORDER BY v_id ASC';
            $cursor = $cnx->query($query);

            //Display all of the products
            //Utilize number_format in the case the cents after markup ends in 0
            echo '<table class="prices_generated"; style="width: 100%">';
            echo '<tr>
                        <th>Manufacturer</th>
                        <th>Model</th>
                        <th>Year</th>
                        <th>Condition</th>
                        <th>Cylinders</th>
                        <th>Fuel</th>
                        <th>Mileage</th>
                        <th>Title Status</th>
                        <th>Transmission</th>
                        <th>Drivetrain</th>
                        <th>Vehicle Type</th>
                        <th>Paint Color</th>
                        <th>State of Sale</th>
                        <th>Price Generated</th>
                    </tr>';
            while ($row = $cursor->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['v_manufacturer'] . '</td><td>' . $row['v_model'] . '</td><td>' . $row['v_year'] . '</td>
                        <td>' . $row['v_condition'] . '</td><td>' . $row['v_cylinders'] . '</td><td>' . $row['v_fuel'] . '</td>
                        <td>' . $row['v_odometer'] . '</td><td>' . $row['v_title_status'] . '</td><td>' . $row['v_transmission'] . '</td>
                        <td>' . $row['v_drive'] . '</td><td>' . $row['v_type'] . '</td><td>' . $row['v_paint_color'] . '</td>
                        <td>' . $row['v_state'] . '</td><td>$' . $row['v_price'] . '</td>';
                echo '</tr>';
            }
            echo '</table>';

            $cnx->close();
        ?>
        </table>
    </body>
</html>