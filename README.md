# 🐗 B0AR MVC 🐗

## Init application by running the setup migration 
###### php migrations.php

# Models

##### Object should be placed inside models directory and extend Entity
##### Properties can be found by
##### $product = new ProductModel();
##### $product->myValue;
### Static methods are also present per default and be extended as you wish
##### $products = ProductModel::all(); / $specificProducts = ProductModel::search(['key' => 'value]);


# Build queries through 

###### Application::$app->connection
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
