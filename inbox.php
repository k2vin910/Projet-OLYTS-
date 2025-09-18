<?php
session_start();
if (!isset($_SESSION["identite"])) {
    header("Location: index.php");
    exit;
}

$bdd = new PDO("mysql:host=sql113.infinityfree.com;dbname=if0_39961547_olyts;charset=utf8", "if0_39961547", "GnBN8eTGaQ6hcD");
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$identite_id = $_SESSION["identite"];

// Fetch messages for this user
$stmt = $bdd->prepare("SELECT id_messagerie, sender_id, content FROM messagerie WHERE receiver_id = :uid");
$stmt->execute([":uid" => $identite_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Delete after fetch (view-once)
$bdd->prepare("DELETE FROM messagerie WHERE receiver_id = :uid")->execute([":uid" => $identite_id]);

// Helper to get usernames
function username($id, $bdd) {
    $s = $bdd->prepare("SELECT username FROM identite WHERE id = :id");
    $s->execute([":id" => $id]);
    $r = $s->fetch(PDO::FETCH_ASSOC);
    return $r ? $r["username"] : "Unknown";
}

// Get all users for dropdown (excluding self)
$users = $bdd->prepare("SELECT id, username FROM identite WHERE id != :id");
$users->execute([":id" => $identite_id]);
$users = $users->fetchAll(PDO::FETCH_ASSOC);
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
    <div class="nav">
      <a href="send.php" class="btn small">✉️ New Message</a>
      <a href="index.php" class="btn small danger">Logout</a>
    </div>
    <hr>

    <?php if ($messages): ?>
      <?php foreach ($messages as $m): ?>
        <div class="message-card">
          <b>From <?= htmlspecialchars(username($m["sender_id"], $bdd)) ?>:</b><br>
          <?= htmlspecialchars($m["content"]) ?><br>
          <form action="send.php" method="POST" class="reply-form">
            <input type="hidden" name="reply_to" value="<?= $m["sender_id"] ?>">
            <input type="hidden" name="nfc_id" value="<?= $_SESSION["nfc_id"] ?>">
            <textarea name="message" rows="2" required></textarea>
            <button type="submit" class="btn small">Reply</button>
          </form>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="empty">No new messages.</p>
    <?php endif; ?>
  </div>
</body>
</html>
