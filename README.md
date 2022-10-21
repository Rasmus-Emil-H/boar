# 🐗 B0AR MVC 🐗

## Init application by running the setup migration 
###### php migrations.php

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

## curl

###### $curl = new curl();
###### $curl->setUrl('https://superfunresource.oi')
###### ->setMethod('post')
###### ->setData(['foo' => 'bar'])
###### ->setHeaders(['bla:bla'])
###### ->send();