# 🐗 B0AR MVC 🐗
###### Mvc like PHP application

## Init application by running the setup migration 
###### Create your database
###### Update index files with valid credentials and run script below
###### php migrations.php

## Models

###### Object should be placed inside models directory and extend Entity
###### Properties can be found by
###### $product = new SomeModel($modelPrimaryKey);
###### Is an invalid/no key not set, a object based on your table will be returned
###### Can get values by: $product->get('myValue');
###### Can set values by: $product->set([$field => $value]);
###### Once you're done remember to: $product->save();
###### Static methods are also present per default and be extended as you wish
###### $products = ProductModel::all(); / $specificProducts = ProductModel::search(['key' => 'value]);

## Relations
###### Relations between objects should be done by defining a method on your Model as such:
###### public function images() {
######     $imageModel = $this->hasMany($this->getRelatedObject('images'));
###### }
###### Where "images" is a key based on your models as below
###### protected array $relatedObjects = ['images' => Image::class];


## Build queries through 

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
