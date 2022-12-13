#!/usr/bin/env python

"""
This python program will predict the price
of a used vehicle based on the inputs a
user provides on the website.
"""

import sys
import numpy as np
import pandas as pd
import pickle
import warnings
warnings.filterwarnings(action='ignore', category=UserWarning)
warnings.filterwarnings(action='ignore', category=DeprecationWarning)

#This function will prepare the data needed to predict with the loaded model
def prepare_data():
    #Cap the rows to 10000
    num_rows = 10000
    data = pd.read_csv("Kaggle_Data/vehicles.csv", nrows=num_rows)
    #Create a dataframe of the dataset

    df = pd.DataFrame(data)

    #Drop columns that will not be useful for the model
    df = df.drop(columns=['id', 'url', 'region', 'region_url', 'VIN', 'size', 
                        'image_url', 'description', 'county', 'lat', 'long',
                        'posting_date'], axis=1)

    #Remove rows that do not have at least 12 columns with data
    df = df.dropna(axis=0, thresh=12)

    #Deal with the cylinders column, just want the number
    df = df.assign(cylinders = lambda x: x['cylinders'].str.extract('(\d+)'))
    df['cylinders'] = pd.to_numeric(df['cylinders'])

    #Remove rows where NaN value in float columns
    df = df.dropna(subset=['year', 'cylinders', 'odometer'])

    #Fill missing values in categorical columns
    df = df.fillna('')

    #Rearrange the order of the columns
    df = df[['manufacturer', 'model', 'year', 'condition', 'cylinders', 'fuel',
            'odometer', 'title_status', 'transmission', 'drive', 'type',
            'paint_color', 'state', 'price']]

    #Data X,y split
    X = pd.DataFrame(df, columns=['manufacturer', 'model', 'year', 
                                            'condition', 'cylinders', 'fuel',
                                            'odometer', 'title_status', 
                                            'transmission', 'drive', 'type',
                                            'paint_color', 'state'])
    Y = pd.DataFrame(df, columns=['price'])

    #Convert/encode the string columns to a number
    X = pd.get_dummies(data=X, drop_first=True)

    return X, Y


#Function to make predictions given info about a vehicle as input
def predict_price(manufacturer, model, year, condition, cylinders,
                    fuel, odometer, title_status, transmission,
                    drive, type, paint_color, state):
    #Clean up the manufacturer
    manufacturer = manufacturer.lower()
    manufacturer = "manufacturer_" + manufacturer
    #Clean up the model
    model = model.lower()
    model = "model_" + model
    #Clean up the condition
    condition = condition.lower()
    condition = "condition_" + condition
    #Clean up the fuel
    fuel = fuel.lower()
    fuel = "fuel_" + fuel
    #Clean up the title status
    title_status = title_status.lower()
    title_status = "title_status_" + title_status
    #Clean up the transmission
    transmission = transmission.lower()
    transmission = "transmission_" + transmission
    #Clean up the drive
    drive = drive.lower()
    drive = "drive_" + drive
    #Clean up the type
    type = type.lower()
    type = "type_" + type
    #Clean up the paint color
    paint_color = paint_color.lower()
    paint_color = "paint_color_" + paint_color
    #Clean up the state
    state = state.lower()
    state = "state_" + state
    
    #Find the columns that correspond to the input
    manufacturer_index = np.where(X.columns==manufacturer)[0]
    model_index = np.where(X.columns==model)[0]
    condition_index = np.where(X.columns==condition)[0]
    fuel_index = np.where(X.columns==fuel)[0]
    title_status_index = np.where(X.columns==title_status)[0]
    transmission_index = np.where(X.columns==transmission)[0]
    drive_index = np.where(X.columns==drive)[0]
    type_index = np.where(X.columns==type)[0]
    paint_color_index = np.where(X.columns==paint_color)[0]
    state_index = np.where(X.columns==state)[0]

    #Create an empty row for predicting, 1799
    x = np.zeros(len(X.columns))

    #Assign the categorical columsn that correspond to the input to 1
    if manufacturer_index >= 0:
        x[manufacturer_index] = 1
    if model_index >= 0:
        x[model_index] = 1
    if condition_index >= 0:
        x[condition_index] = 1
    if fuel_index >= 0:
        x[fuel_index] = 1
    if title_status_index >= 0:
        x[title_status_index] = 1
    if transmission_index >= 0:
        x[transmission_index] = 1
    if drive_index >= 0:
        x[drive_index] = 1
    if type_index >= 0:
        x[type_index] = 1
    if paint_color_index >= 0:
        x[paint_color_index] = 1
    if state_index >= 0:
        x[state_index] = 1

    #Populate these special columns (not categorical)
    x[0] = year
    x[1] = cylinders
    x[2] = odometer
        
    #Return the predicted price as an int to correspond with the prices in y
    return int(loaded_model.predict([x])[0] + 0.5)

#Program execution begins here
if __name__ == '__main__':

    #Create dictionary to house the information to predict
    predict_info = {'manufacturer': '', 'model': '', 'year': '', 'condition': '', 'cylinders': '',
                    'fuel': '', 'odometer': '', 'title_status': '', 'transmission': '', 'drive': '',
                    'type': '', 'paint_color': '', 'state': ''}

    val_index = 1
    #Insert the data passed into a dictionary used by the predict_price function
    for key in predict_info:
        if(sys.argv[val_index] == "notset"):
            predict_info[key] = ""
        else:
            predict_info[key] = sys.argv[val_index]
        val_index = val_index + 1

    #Prepare the data and return the X and Y dataframes
    X, Y = prepare_data()

    #Load the saved model
    loaded_model = pickle.load(open('model/usedcar_model.sav', 'rb'))

    #Run the predict_price function on the user input
    prediction = predict_price(predict_info['manufacturer'], predict_info['model'], float(predict_info['year']),
                                 predict_info['condition'], float(predict_info['cylinders']), predict_info['fuel'],
                                 float(predict_info['odometer']), predict_info['title_status'], predict_info['transmission'],
                                 predict_info['drive'], predict_info['type'], predict_info['paint_color'], predict_info['state'])

    #Print the result
    print(prediction)