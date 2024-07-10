# Boar PHP minimal MVC 🐗

## Overview

Boar is a lightweight PHP MVC framework designed to facilitate the creation of web applications. 

It follows the Model-View-Controller (MVC) architectural pattern, providing a separation of concerns, making it easier to manage and maintain your application.

## Features

- **MVC Architecture**: Separates the application logic (Model), user interface (View), and control flow (Controller).
- **Routing**: A simple and flexible routing system to map URLs to controllers and actions.
- **Dependency Injection**: Easily manage dependencies and services within the application.
- **Configuration Management**: Centralized configuration for different environments.
- **Template Engine**: Use of PHP as a templating engine for rendering views.
- **Error Handling**: Error handling and logging mechanism.
- **Session Management**: Built-in session handling for managing user sessions.
- **CSRF Protection**: Cross-Site Request Forgery protection for form submissions.
- **Validation**: Input validation for ensuring data integrity.

### app/

Contains the main application code.

- **config/**: Configuration files for the application.
- **controllers/**: Controller classes responsible for handling requests and returning responses.
- **models/**: Model classes for interacting with the database.
- **views/**: View templates for rendering HTML.
- **Core/**: Core framework classes that provide the foundation for the MVC framework.

### public/

The public-facing directory that serves as the entry point for the application.

- **index.php**: The main entry point of the application, initializing the framework.
- **.htaccess**: Configuration for URL rewriting to route all requests through `index.php`.

### vendor/

Contains third-party libraries and dependencies managed via Composer.

## Configuration

### ~/static/setup.json

The `setup.json` file contains configuration settings for your application.

The setup file includes, by default;

Database, client assets, request configs, allowed file types, integrations, states, etc

## Helpers

### yard.php

A global yard.php file is provided for oftenly used methods, function, in order to reduce specific namespace contexts.

This yard file by default provides the app() function that will grant you access to the application instance from where you can get the global objects that is being set at bootstrapping.

### Controllers

Creating a controller is straightforward, either cp one of existing or create a new as below.

```
<?php

namespace app\controllers;

use \app\core\src\Controller;

class LanguageController extends Controller {

    public function index() {

    }

}
```

Should you make a request to a controller without specifying a method, the index method will try to run

Controllers have access to various methods that can help you ease your development experience, some are listed below;

denyGETRequest(), denyPOSTRequest(), isGet(), isPost()

### Models

Creating a model is straightforward, either cp one of existing or create a new as below

```
<?php

namespace app\models;

use \app\core\src\database\Entity;

final class LanguageModel extends Entity {

	public function getTableName(): string {
		return 'Languages';
	}
	
	public function getKeyField(): string {
		return 'LanguageID';
	}
    
}
```

Models extends the Entity that has access to various methods that will ease the way you interact with the database

### File handling

POSTing a file to /file will automatically handle the file for you and mv it to the uploads dir and insert a row into the database, based on the requested entity

### Query building

Boar comes with a querybuilder, located at core/src/database and can be accessed directly on the models by doing

```
(new LanguageModel())->query()
```

The code above will instansiate a new query builder based on the Language table from where you can chain 

```
->select()->where()->run(); 

// Or debug current query by ->debugQuery(); instead of run();
```

### Database migrations

Making changes to the database should be done via a migration.

Migrations are located under the ~/migrations dir and examples are implemented.

### WAF

A minor web application firewall is the first object being constructed.
Adjust the rules and filters to your needs.