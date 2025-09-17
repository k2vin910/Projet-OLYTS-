<?php
session_start();

// Connect to MySQL (adjust if your user/pass/db are different)
$bdd = new PDO("mysql:host=localhost;dbname=olyts;charset=utf8", "root", "");
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["username"])) {
    $username = trim($_POST["username"]);

    // find or create user
    $stmt = $bdd->prepare("SELECT id FROM identite WHERE username = :u");
    $stmt->execute([":u" => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $stmt = $bdd->prepare("INSERT INTO identite (username) VALUES (:u)");
        $stmt->execute([":u" => $username]);
        $identite_id = $bdd->lastInsertId();  
    } else {
        $identite_id = $user["id"];
    }

    // store in session
    $_SESSION["identite"] = $identite_id;
    $_SESSION["username"] = $username;

    // redirect to inbox
    header("Location: inbox.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Secret Messages - Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="box">
    <h2>🔐 Secret Messages</h2>
    <form method="POST" action="index.php">

      <input type="text" name="username" placeholder="Enter username" required style="width:100%;padding:12px;border-radius:8px;margin-bottom:12px;border:1px solid #444;background:#121528;color:#eee;">
      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>
