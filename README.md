#  B0AR MVC 🐗
### Mvc like PHP application

#### Init application by running the setup migration 
###### Create your database
###### Update index files with valid credentials and "php migrations.php"

#### Models
###### Object should be placed inside models directory and extend Entity
###### Properties can be found by
###### $product = new SomeModel($modelPrimaryKey);
###### Is an invalid/no key not set, a object based on your table will be returned
###### Can get values by: $product->get('myValue');
###### Can set values by: $product->set([$field => $value]);
###### Once you're done remember to: $product->save();
###### Static methods are also present per default and be extended as you wish
###### $products = ProductModel::all(); / $specificProducts = ProductModel::search(['key' => 'value]);

#### Relations
###### Relations between objects should be done by defining a method on your Model as such:
###### public function images() {
######     return $this->hasMany(ImageModel::class);
###### }

#### Build manual queries through 
###### Application::$app->connection
###### ->select('Users u', ['u.email', 'u.firstname', 'p.body')
###### ->join('Posts p', 'UserID')
###### ->where(['u.status' => '0'])
###### ->limit(100)
###### ->execute();

#### Handle files through 
###### Application::$app->file

#### Request response cycle through 

###### Response / Request handlers

#### Custom middlewares at core\middlewares

###### Auth middleware for protected routes

#### Set CSRF tokens through core\tokens\csrf

###### Remember to use tokens for your forms kids!

#### Handle Database changes through migrations

###### @migrations folder

### Service workers
#### A service worker will automatically be avaliable for you, once the application has been initially migrated
#### In the console the worker can be found by boar.serviceWorker

### IndexedDB
#### A connection to Indexed DB will automatically be avaliable for you, once the application has been initially migrated
#### In the console the worker can be found by boar.indexedDB
#### Do not use this for sensitive data