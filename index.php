<?php
    //Remove form error message when clicking back button after successful purchase
    header("Cache-Control: no-store, no-cache, must-revalidate");
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Used Car Price Generator</title>
        <script>
            function submitForm() {
                //Clear the form when submit
                var frm = document.getElementById("generate_form");
                frm.submit(); // Submit the form
                frm.reset();  // Reset all form data
                return false; // Prevent page refresh
            }
        </script>
        <link rel="stylesheet" href="styles.css">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="table.php">Prices Generated</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
    </head>
    
    <body>
        <h1>Used Car Price Generator: Home</h1>
        <br>
        <h3>Enter the information about your vehicle in the form below.</h3>   
        <h3>Select "Get Price" once done to see the estimated price.</h3>
    </body>
    <div class="container">
        <form class="center" method="POST" id="generate_form">
            <h1>Price Generator Form</h1>
            <section>
                <h2>Required Information</h2>

                <label for="manufacturer">Manufacturer:</label>
                <input type="text" id="manufacturer" name="manufacturer"/>

                <label for="model">Model:</label>
                <input type="text" id="model" name="model"/>
 
                <label for="year">Year:</label>
                <input type="text" id="year" name="year"/>

                <label for="mileage">Mileage:</label>
                <input type="text" id="mileage" name="mileage"/>
            </section>
            <section>
                <h2>Optional Information</h2>
                <h4><i>Note: The more fields you fill in the more accurate the price generated will be.</i></h4>

                <label for="condition">Condition:</label>
                <input type="text" id="condition" name="condition" placeholder="Ex: good, fair, poor"/>

                <label for="cylinders">Number of Cylinders:</label>
                <input type="text" id="cylinders" name="cylinders"/>

                <label for="fuel">Fuel Type:</label>
                <input type="text" id="fuel" name="fuel" placeholder="Ex: gas, diesel"/>

                <label for="title_status">Title Status:</label>
                <input type="text" id="title_status" name="title_status" placeholder="Ex: clean, salvage"/>

                <label for="transmission">Transmission:</label>
                <input type="text" id="transmission" name="transmission"/>

                <label for="drive">Drivetrain:</label>
                <input type="text" id="drive" name="drive" placeholder="Ex: fwd, rwd, 4wd"/>

                <label for="type">Vehicle Type:</label>
                <input type="text" id="type" name="type" placeholder="Ex: sedan, coupe, van"/>

                <label for="paint_color">Paint Color:</label>
                <input type="text" id="paint_color" name="paint_color"/>

                <label for="state">State of Sale:</label>
                <input type="text" id="state" name="state" placeholder="Ex: nj, AZ, sc"/>
            <section>
                <br>
                <input type="button" value="Get Price" id="btnsubmit" onclick="submitForm()">
            </section>
        </form>
    </div>

    <footer>
        <script>
            if(window.history.replaceState) 
            {
                //Reset the form on refresh
                document.getElementById("generate_form").reset();
                window.history.replaceState(null, null, window.location.href);
            }
        </script>
    </footer>
</html>

<?php
    //Functions used when the form is submitted

    //This function will insert the form data into the database
    function insertDB(array $arr, $price)
    {
        $cnx = new mysqli('localhost', 'username', 'password', 'usedcars');

        if ($cnx->connect_error) 
            die("Connection failed: " . $cnx->connect_error);

        //Changed to a prepared statement with parameter binding
        $sql = $cnx->prepare("INSERT INTO predictions (v_manufacturer, v_model, v_year, v_condition, v_cylinders,
                                v_fuel, v_odometer, v_title_status, v_transmission, v_drive, v_type, v_paint_color, v_state, v_price)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");

        //Assign the parameters
        $sql->bind_param("ssssssssssssss", $arr[0], $arr[1], $arr[2], $arr[3], $arr[4], $arr[5], $arr[6], $arr[7], $arr[8], $arr[9], $arr[10], $arr[11], $arr[12], $price);

        //Check if the query was successful
        try
        {
            $sql->execute();
        } 
        catch (PDOException $e)
        {
            echo "Error: " . $sql . "<br>" . $e->getMessage();
        }

        $cnx->close();
    }

    //This function will call the python script that uses the model to get a price
    function getPrice(array $arr)
    {
        //This code runs the price_generator program
        $command_str = "python model/price_generator.py $arr[0] $arr[1] $arr[2] $arr[3] $arr[4] $arr[5] $arr[6] $arr[7] $arr[8] $arr[9] $arr[10] $arr[11] $arr[12]";
        $escaped_cmd = escapeshellcmd($command_str);
        $output = shell_exec($escaped_cmd);
        echo nl2br("\n");

        //Check if the model does not output a price for that input
        if($output == "")
        {
            echo "A price could not be generated.";
            echo nl2br("\n");
            echo "Consider adding more information and try again.";
            echo nl2br("\n");
            echo "Also, ensure there are no mistakes in the information entered.";
        }
        else
        {
            //Got a number, print it
            echo "<p align='center'> <font size='6pt'>Vehicle Price: $$output</font> </p>";
        }

        //Return the output from the model
        return $output;
    }
?>

<?php
    //Handle the form being submitted here

    if($_SERVER["REQUEST_METHOD"] == "POST") 
    {
        //Grab the values entered in the form
        $manufacturer = $_POST["manufacturer"];
        $model = $_POST["model"];
        $year = $_POST["year"];
        $odometer = $_POST["mileage"];

        //Ensure all the required fields are filled at minimum
        if(empty($manufacturer) || empty($model) || empty($year) || empty($odometer))
        {
            //One or more fields empty, fail purchase
            echo nl2br("\n");   //Push error further down
            echo "<p align='center'> <font color='red' size='6pt'>Must fill in the required fields at minimum.</font> </p>";
        }
        else
        {
            //Grab the rest of the variables here
            $condition = $_POST["condition"];
            $cylinders = $_POST["cylinders"];
            $fuel = $_POST["fuel"];
            $title_status = $_POST["title_status"];
            $transmission = $_POST["transmission"];
            $drive = $_POST["drive"];
            $type = $_POST["type"];
            $paint_color = $_POST["paint_color"];
            $state = $_POST["state"];

            //Create an array composed of the information from the form
            $input_array = array(
                0 => $manufacturer,
                1 => $model,
                2 => $year,
                3 => $condition,
                4 => $cylinders,
                5 => $fuel,
                6 => $odometer,
                7 => $title_status,
                8 => $transmission,
                9 => $drive,
                10 => $type,
                11 => $paint_color,
                12 => $state
            );

            //Set array value equal to notset if it is blank, needed to pass to python program
            foreach ($input_array as $x => $info)
            {
                if($info == "")
                {
                    $input_array[$x] = "notset";
                }
                //Trim each input values just in case
                $input_array[$x] = trim($input_array[$x]);
            }

            //Remove values in POST
            unset($_POST);

            //Use this to not insert if no price generated
            $gotPrice = false;

            //Get the price for the information entered
            $price = getPrice($input_array);

            //Remove the new line at end of price string
            if($price != NULL)
            {
                $price = rtrim($price);
                $gotPrice = true;
            }

            //Set array value to blank if it is notset
            foreach ($input_array as $x => $info)
            {
                if($info == "notset")
                {
                    $input_array[$x] = "";
                }
            }

            //Insert into transactions table in DB here
            if($gotPrice)
            {
                insertDB($input_array, $price);
            }
        }
    }
?>