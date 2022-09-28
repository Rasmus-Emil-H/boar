<?php 
    use app\core\Application;

    $query = Application::$app->database
        ->startQuery("Users", "*", ["status" => "0"])
        ->fetch()
        ->execute();

    var_dump($query);

?>
<h2>home</h2>