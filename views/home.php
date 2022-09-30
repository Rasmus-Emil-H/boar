<?php 
    use app\core\Application;

    $query = Application::$app->database
        ->init("Users", ["status" => "0"])
        ->select()
        ->execute();

    var_dump($query);

?>
<h2>home</h2>