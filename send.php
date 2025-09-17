<?php
session_start();
if (!isset($_SESSION["identite"])) {
    header("Location: index.php");
    exit;
}

// connect to MySQL
$db = new PDO("mysql:host=localhost;dbname=olyts;charset=utf8", "root", "");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$success = null;
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["message"]) && !empty($_POST["receiver"])) {
    $stmt = $db->prepare("INSERT INTO messagerie (sender_id, receiver_id, content) VALUES (:s, :r, :c)");
    $stmt->execute([
        ":s" => $_SESSION["identite"],
        ":r" => (int)$_POST["receiver"],
        ":c" => $_POST["message"]
    ]);
    $success = "✅ Message sent!";
}

// get all users except the current one
$users = $db->prepare("SELECT id, username FROM identite WHERE id != :id");
$users->execute([":id" => $_SESSION["identite"]]);
$users = $users->fetchAll(PDO::FETCH_ASSOC);

// if reply (from inbox)
$replyTo = $_POST["reply_to"] ?? null;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Send Message</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="box">
    <h2>✉️ Send a Message</h2>
    <form method="POST">
      <select name="receiver" required style="width:100%;padding:8px;margin-bottom:12px;">
        <option value="">Select recipient</option>
        <?php foreach ($users as $u): ?>
          <option value="<?= $u["id"] ?>" <?= $replyTo==$u["id"]?"selected":"" ?>>
            <?= htmlspecialchars($u["username"]) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <textarea name="message" rows="4" placeholder="Type your message..." required></textarea>
      <button type="submit">Send</button>
    </form>
    <?php if ($success): ?><div class="message"><?= $success ?></div><?php endif; ?>
  </div>
</body>
</html>
