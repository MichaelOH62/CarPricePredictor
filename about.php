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
        <h1>Used Car Price Generator: About</h1>
        <br>
        <h3>Project Overview:</h3>
        <div class="paragraphs">
            <p>
                <!-- Brief intro here -->
                The goal of this project was to utilize Artificial Intelligence,
                and more specifically Machine Learning, to create a model that
                would be able to accurately predict the price of a vehicle.
                Once the model was created, the next goal was to allow
                a user to enter information about a vehicle and receive a
                price back from the model. Finally, once user input was working
                the final component was to create a web application to have a
                more interactive way of generating prices from the model. 
                <br><br>
                The two major components of the project are explained in more detail below.
            </p>
        </div>
        <br>
        <h3>Machine Learning:</h3>
        <div class="paragraphs">
            <p>
                <!-- Explain the model component here -->
                The machine learning aspect of this project covered creating
                the model that would be able to make predictions. To do this,
                I started by looking for datasets available containing information
                on used vehicle listings. I found a dataset on Kaggle by Austin Reese 
                containing used vehicle listings scraped from Craigslist. 
                The next matter of business was to read in the data and perform data 
                preprocessing on it to get it into a state that meaning could be extracted.
                <br><br>
                I utilized numpy, pandas, and sklearn to preprocess the data.
                I created a pandas dataframe to hold all of the data read in from
                the csv file containing the vehicle information. I had to cap the
                number of rows read in to 10000 due to memory restrictions.
                Then, I performed multiple operations on the data to get it into a
                state that it could be trained, such as dealing with NULL/NaN values
                and dropping columns that are not needed or useful. I used sklearn to
                split the data into X and y training and test sets.
                <br><br>
                Next, I used the RandomForestRegressor() class provided by sklearn
                as the model for the predictions. After running the model several times,
                I found it to have an average accuracy of 81%, which I deemed sufficient
                for the purposes of this project. I saved the model locally using pickle
                and that model is loaded every time the form is submitted and a price
                is generated.
                <br><br>
                Finally, I wanted to make the model interactive to a user, so I wrote a
                function called predict_price() in Python that would take in user input 
                and send the information to the model to output a price.
            </p>
        </div>
        <br>
        <h3>Web Development:</h3>
        <div class="paragraphs">
            <p>
                <!-- Explain the website component here -->
                Once I had the model able to make accurate predictions based
                on user input, I wanted to create a website that would allow
                a user to enter used vehicle information into a HTML form.
                <br><br>
                I started by planning out the pages that the website would have.
                The main (Home) page would have the form that would collect the user
                input and return a price if it was able to predict one. I then decided
                I wanted to integrate a MySQL database into my project, so I would create
                a page (Prices Generated) that would display a list of the 10 most recent
                submissions pulled from the MySQL database. Finally, I also wanted a
                page that would explain the project and website, which is the current page.
                <br><br>
                I utilized Python, PHP, HTML, CSS, and SQL to create this website!
            </p>
        </div>
    </body>
</html>