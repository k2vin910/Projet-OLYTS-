<?php
session_start();
if (!isset($_SESSION["identite"])) {
    header("Location: index.php");
    exit;
}

// connect to MySQL
$bdd = new PDO("mysql:host=localhost;dbname=olyts;charset=utf8", "root", "");
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$identite_id = $_SESSION["identite"];

// Fetch messages for this user
$stmt = $bdd->prepare("SELECT id, sender_id, content FROM messagerie WHERE receiver_id = :uid");
$stmt->execute([":uid" => $identite_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Delete after fetch (view-once behavior)
$bdd->prepare("DELETE FROM messagerie WHERE receiver_id = :uid")->execute([":uid" => $identite_id]);

// Helper to get usernames
function username($id, $bdd) {
    $s = $bdd->prepare("SELECT username FROM identite WHERE id = :id");
    $s->execute([":id" => $id]);
    $r = $s->fetch(PDO::FETCH_ASSOC);
    return $r ? $r["username"] : "Unknown";
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Inbox</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="box">
    <h2>📥 Inbox - <?= htmlspecialchars($_SESSION["username"]) ?></h2>
    <a href="send.php">✉️ New Message</a> | <a href="index.php">Logout</a>
    <hr>
    <?php if ($messages): ?>
      <?php foreach ($messages as $m): ?>
        <div class="message">
          <b>From <?= htmlspecialchars(username($m["sender_id"], $bdd)) ?>:</b><br>
          <?= htmlspecialchars($m["content"]) ?><br>
          <form action="send.php" method="POST" style="margin-top:8px;">
            <input type="hidden" name="reply_to" value="<?= $m["sender_id"] ?>">
            <textarea name="message" rows="2" required></textarea>
            <button type="submit">Reply</button>
          </form>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No new messages.</p>
    <?php endif; ?>
  </div>
</body>
</html>
