<?php 
    use app\core\Application;

    $query = Application::$app->database
        ->select("Users", ["email"])
        ->where(["status" => "0"])
        ->limit(1)
        ->execute();

    var_dump($query);

?>
<h2>home</h2>