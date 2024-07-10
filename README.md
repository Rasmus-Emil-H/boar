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

### core/src

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

## Setup

git clone https://github.com/Troldefar/boar.git && cd boar && composer install

Update the ~/static/setup.json (Database credentials)

Run the command: php public/index.php DatabaseMigration

Observe default tables with default values are now set up and the application is ready to go

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

### Middlewares

Should you wish to execute logic before methods run, you can provide a middleware that extends ~/core/src/middlewares/Middleware.

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

### Entity relations

A default implementation of table relations has been created and can be found at ~/core/src/traits/EntityRelationsTrait from where you can describe relations based on your entities. Default methods has been provided and can be accesses like this (hasMany)

```
<?php

namespace app\models;

use \app\core\src\database\Entity;

final class LanguageModel extends Entity {

	public function translations() {
		return $this->hasMany(TranslationModel::class)->run();
	}
	
}
```

## Websocket

A default "no library" websocket is avaliable and can be run via

```
nohup php public/index.php WebsocketInit & 

// (Or however you like)
```

And in main.js include

```
await window.boar.websocket.init();
```

## CLI

### Tools

Three different CLI tools are provided ouf of the box and can be found ~/core/src/CLI.php

Provided more as your application grow

## Cron jobs

### Scheduling

Boar comes with built in cron functionality.

To run the cron manager you can do as below:

```
* * * * * php ~/public/index.php CronjobScheduler
```

Once this is setup you touch a file in ~/core/src/scheduling 

(TestScheduler is already provided) and must be added to the CronJob table as an entry with CronjobEntity = 'TestScheduler'


## Database migrations

Making changes to the database should be done via a migration.

Migrations are located under the ~/migrations dir and examples are implemented.

Example below where we have two methods, up for creating the table and down for dropping the table.

The second argument provided for up is the closure from where you can set column and determine keys on your table.


File name must match the class name.

```
<?php

use \app\core\src\database\table\Table;
use \app\core\src\database\Schema;

class add_translations_table_2018_12_16_0001 {

    public function up() {
        (new Schema())->up('Translations', function(Table $table) {
            $table->increments('TranslationID');
            $table->varchar('Translation', 50);
            $table->varchar('TranslationHumanReadable', 100);
            $table->integer('LanguageID', 2);
            $table->varchar('TranslationHash', 50);
            $table->timestamp();
            $table->primaryKey('TranslationID');
            $table->foreignKey('LanguageID', 'Languages', 'LanguageID');
        });
    }

    public function down() {
        (new Schema())->down('Translations'); 
    }

}
```

Once you are ready you can run php public/index.php DatabaseMigration and observe that your table has been created with the correct columns, types and relations.

## WAF

A minor web application firewall is the first object being constructed.
Adjust the rules and filters to your needs.

## Frontend

### Javascript

Located at ~/public/resources/js/main.js you can import objects that you include in ./modules, for some modularity

Once you include new paths to modulesToImport they will be avaliable at window.boar.YOUR_MODULE_NAME

Please note that objects will be frozen