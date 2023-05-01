# 🐗 B0AR MVC 🐗

## Init application by running the setup migration 
###### php migrations.php

# Models

##### Object should be placed inside models directory and extend Entity
##### Properties can be found by $static->myValue


# Build queries through 

###### Application::$app->database
###### ->select("Users u", ["u.email", "u.firstname", "p.body"])
###### ->join('Posts p', 'UserID')
###### ->where(["u.status" => "0"])
###### ->limit()
###### ->execute();

## Handle files through 

###### Application::$app->file

## Request response cycle through 

###### Response / Request handlers

## Custom middlewares at core\middlewares

###### Auth middleware for protected routes

## Set CSRF tokens through core\tokens\csrf

###### Remember to use tokens for your forms kids!

## Handle Database changes through migrations

###### @migrations folder
