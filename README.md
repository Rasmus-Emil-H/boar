# Boar PHP minimal MVC 🐗

<details>
    <summary>Intro</summary>

    Boar is a lightweight PHP MVC framework designed to facilitate the creation of web applications.

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

    ### ~

    Contains the main application code.

    - **static/**: Configuration files for the application.
    - **controllers/**: Controller classes responsible for handling requests and returning responses.
    - **models/**: Model classes for interacting with the database.
    - **views/**: View templates for rendering HTML.
    - **core/src/**: Core framework classes that provide the foundation for the MVC framework.

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
</details>

<details>
    <summary>Views</summary>

    ### Templates

    Views are the file that your browser renders, they should be set by the controller and be located at ~/views and follow the .tpl.php extension

    You can return a view, and variable to that view in your controller

    ```
    <?php

    namespace app\controllers;

    use \app\core\src\Controller;

    class HomeController extends Controller {

        public function index() {
            $this->denyPOSTRequest();
            
            $this->setFrontendTemplateAndData(templateFile: 'languages', data: ['boar' => 'is live and running']);
        }

    }
    ```

    The data array can now be directly accesed from the frontend file

    languages.tpl.php
    ```
    <?= hs($boar); ?>
    ```

    ### Layouts

    If you need differents layouts you can specify them by doing the following:

    ```
    <?php

    namespace app\controllers;

    class AuthController extends Controller {

        public function login(): void {
            $this->setClientLayoutStructure(layout: 'auth', view: 'login');
        }
        
    }
    ```

    The layout must be located in ~/views/layouts dir
</details>

<details>
    <summary>Controllers</summary>

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

    Should you wish to execute logic before methods run, you can provide a middleware that extends ~/core/src/middlewares/Middleware.</details>
</details>

<details>
    <summary>Models</summary>

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

    ### Patching entities

    Patching can become quite cumbersome, and because of that, boar comes with various methods on children of Entity that allows you to
    create, read, update and delete on Entities, without having to repeat yourself to much.

    Example of entity methods below

    ```
    $cLanguage = new LanguageModel(N);

    // Patching
    $cLanguage->patchField(['Name' => $arguments->Name]);

    $cLanguage->complete();
    $cLanguage->findOrCreate();
    $cLanguage->init($args);
    $cLanguage->edit($args);

    // Add meta data
    $cLanguage->addMetaData(['Meta test']);

    // Deleting
    $cLanguage->delete();
    $cLanguage->softDelete();
    ```

    Other examples of CRUDing things within the entire entity context can be browsed like below

    ```
    $cLanguage = new LanguageModel();

    // Searching
    $cLanguage->find('Name', 'English');
    $cLanguage->search(['Name' => 'English']);

    // Truncating
    $cLanguage->truncate();
    ```
</details>

<details>
    <summary>Controller - Method interaction</summary>

    A controller should always resolve to a model, this can happen in various ways but a default implemented way happens in ~/core/src/traits/ControllerMethodTrait.php

    There are three default method provided (edit, view and delete) which dispatches the dispatchMethodOnEntity method.

    You can create methods however you like and dispatchMethodOnEntity should be seen as a base for automatically creating the entity with the returnValidEntityIfExists method, then getting the body from the Request object and dispatching the method on the model.

    ### Allowed http methods from the model

    The allowed http methods should be manually be specified to avoid fuzzing and other jacksters. Like below.


    ```
    <?php

    namespace app\models;

    use \app\core\src\database\Entity;

    final class LanguageModel extends Entity {

        protected array $ALLOWED_HTTP_METHODS = [
            'getTranslations', 'create', 'delete'
        ];

    }
    ```

    If you forget to include your method in the ALLOWED_HTTP_METHODS array, a method not allowed response will be returned to the client.

    When you want to make a request from you client, an example would be


    POST controller/method/primarykey
    ```
    POST /language/edit/1
    ```

    You can then use

    ```
    $cEntity = $this->returnValidEntityIfExists();
    ```

    In your custom methods, in order for the application to fetch you the correct entity based on the context.
    Should you need a new object or another you would do like below

    ```
    <?php

    namespace app\controllers;

    use \app\core\src\Controller;

    class LanguageController extends Controller {

        public function someMethod() {
            // If the path primary key exists on the proper model, a Entity will be loaded for you, based on the context
            $cLanguage = $this->returnValidEntityIfExists();
            $cLanguage->requireExistence();

            $request = $this->requestBody->body;
            $response = $cLanguage->dispatchHTTPMethod($request->action, $request);

            $this->response->{$this->determineClientResponseMethod(dispatchedHTTPMethodResult: $response)}($response ?? '');
        }
    }
    ```
</details>

<details>
    <summary>Request - Response cycle</summary>

    Controllers are instansiated with a Request and Response object.

    The Request object are responsible for getting the body from the client, within the proper context, and can be accesed by any controllers like below in the body variable

    ### Request 

    The parent controller setups various things once a controller is instansiated via the factory. 
    These values can be directly fetched from child controllers via

    You can get the query parameters from any controller like below

    ```
    $search = $this->request->getQuerySearchParameters();
    ```

    ### Response

    The response object lets you answer to the client in a predictable and easy way like below with the "ok" method
    The response object has variouos methods for responding and can be extended upon as fits your needs

    ```
    <?php

    use \app\core\src\Controller;

    class LanguageController extends Controller {

        public function someMethod() {
            // Get request body
            $request = $this->requestBody;

            // Do some logic from a model or the controller

            // Respond to the client
            $this->response->ok();
        }
        
    }

    ```
</details>

<details>
    <summary>File handling</summary>

    POSTing a file to /file will automatically handle the file for you and mv it to the uploads dir and insert a row into the database, based on the requested entity

    window.boar.behaviour have a default input file listener (uploadFile) so that if you have input type of file with a class of globalFileUploader you can directly upload files without having to do more, however certain data attributes must be present in order to attach the file to the proper entity. (entityType, entityType, type)
</details>

<details>
    <summary>Query building</summary>

    Boar comes with a querybuilder, located at core/src/database and can be accessed directly on the models by doing like below

    ```
    // Will return a new QueryBuilder instance on the corresponding entity
    (new LanguageModel())->query()
    ```

    The code above will instansiate a new query builder based on the Language table from where you can chain 

    ```
    ->select()->where()->run(); 

    // Or debug current query by ->debugQuery(); instead of run();
    ```
</details>

<details>
    <summary>Entity relations</summary>

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
</details>

<details>
    <summary>Websocket</summary>

    A default "no library" websocket is avaliable and can be run via

    ```
    nohup php public/index.php WebsocketInit & 

    // (Or however you like)
    ```

    And in main.js include

    ```
    await window.boar.websocket.init();
    ```
</details>

<details>
    <summary>Exceptions</summary>

    Custom exceptions can be made and should reside within ~/core/src/exceptions and should contain a code(int) and a message (string)
</details>

<details>
    <summary>Gate</summary>

    A default gate implementation is in place and can be used where ever you like

    Gates in this context is meant to be a repetetive reducer by allowing you to specify readable methods with a clear intent, like below

    ProductController.php
    ```
    <?php

    namespace app\controllers;

    use \app\core\src\Controller;
    use \app\core\src\gate\Gate;
    use \app\models\ProductModel;

    class ProductController extends Controller {

        public function edit() {
            $cProduct = $this->returnValidEntityIfExists();

            if (!Gate::isAuthenticatedUserAllowed('canViewProduct', $cProduct)) $this->response->notAllowed();
            
            if ($this->request->isGet())
                return $this->setFrontendTemplateAndData(templateFile: 'editProduct', data: ["product" => $cProduct]);
        }

    }
    ```

    Gate.php
    ```
    <?php

    namespace app\core\src\gate;

    use \app\core\src\database\Entity;
    use \app\core\src\miscellaneous\CoreFunctions;
    use \app\core\src\traits\GateStaticMethodTrait;

    class Gate {

        use GateStaticMethodTrait;

        protected static function canViewProduct(Entity $product): bool {
            $user = CoreFunctions::applicationUser();
            
            return $product->user()->key() === $user->key() || $user->isAdmin();
        }

    }
    ```
</details>

<details>
    <summary>Console commands</summary>

    Boar comes with a very minor command for creating an entity. In your terminal you can type

    ```
    php boar create-entity test
    ```

    The cmd above will create: A controller, a model, a migration file and a view
</details>

<details>
    <summary>Cronjobs</summary>

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
</details>

<details>
    <summary>Database migrations</summary>

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
</details>

<details>
    <summary>WAF</summary>

    A minor web application firewall is the first object being constructed.
    Adjust the rules and filters to your needs.
</details>

<details>
    <summary>Frontend</summary>

    ### Javascript

    Located at ~/public/resources/js/main.js you can import objects that you include in ./modules, for some modularity

    Once you include new paths to modulesToImport they will be avaliable at window.boar.YOUR_MODULE_NAME

    Please note that objects will be frozen

    ### Serviceworker

    Comes with a default serviceworker implementation, use as you wish

    ### Form submissions

    All POST form submissions must include a valid CSRF token, this should be included in the form like below

    ```
    <?= (new \app\core\src\tokens\CsrfToken())->insertHiddenToken(); ?>
    ```

    By default, window.boar.behaviour will intercept all forms and return a promise from which you can do what you want

    In the frontend you can then await this behaviour, or let it submit as normal, and do custom tasks like below

    ```

    $(document).on('click', '.something', async function(e) {
        e.preventDefault();
        const res = await window[appName].behaviour.submitForm($(e.target).closest('form'));
        // Do something with the res
    });
    ```
</details>