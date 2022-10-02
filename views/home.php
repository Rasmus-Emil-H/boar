<?php 
    use app\core\Application;

    $query = Application::$app->database
        ->select("Users u", ["u.email", "u.firstname"])
        ->where(["u.status" => "0"])
        ->limit(1)
        ->execute();

    var_dump($query);

?>
<h2>home</h2>