<?php 
    use app\core\Application;

    $query = Application::$app->database
        ->select("Users u", ["u.email", "u.firstname", "p.body"])
        ->join('Posts p', 'UserID')
        ->where(["u.status" => "0"])
        ->limit()
        ->execute();

?>
<h2>home</h2>