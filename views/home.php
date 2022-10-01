<?php 
    use app\core\Application;

    $query = Application::$app->database
        ->select("Users", ["*"])
        ->where(["status" => 0])
        ->execute();

    var_dump($query);

?>
<h2>home</h2>