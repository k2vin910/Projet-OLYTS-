<?php
session_start();
if (!isset($_SESSION["identite"])) {
    header("Location: index.php");
    exit;
}

$bdd = new PDO("mysql:host=sql113.infinityfree.com;dbname=if0_39961547_olyts;charset=utf8", "if0_39961547", "GnBN8eTGaQ6hcD");
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$success = null;
$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nfc_id = $_POST["nfc_id"] ?? null;
    $message = trim($_POST["message"] ?? "");
    $receiver = $_POST["receiver"] ?? null;
    $replyTo = $_POST["reply_to"] ?? null;

    // Verify NFC tag matches logged-in user
    if ($nfc_id !== $_SESSION["nfc_id"]) {
        $error = "❌ Unauthorized NFC tag!";
    } elseif (!$message) {
        $error = "❌ Message cannot be empty.";
    } else {
        // Use reply_to if set, otherwise selected receiver
        $finalReceiver = $replyTo ?? $receiver;

        if (!$finalReceiver) {
            $error = "❌ No recipient selected.";
        } else {
            $stmt = $bdd->prepare("INSERT INTO messagerie (sender_id, receiver_id, content) VALUES (:s, :r, :c)");
            $stmt->execute([
                ":s" => $_SESSION["identite"],
                ":r" => (int)$finalReceiver,
                ":c" => $message
            ]);
            $success = "✅ Message sent!";
        }
    }
}

// Get all users for dropdown
$users = $bdd->prepare("SELECT id, username FROM identite WHERE id != :id");
$users->execute([":id" => $_SESSION["identite"]]);
$users = $users->fetchAll(PDO::FETCH_ASSOC);

// Pre-select reply if any
$replyTo = $_POST["reply_to"] ?? null;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Send Message</title>
  <link rel="stylesheet" href="style.css">
  <script src="script.js"></script>
</head>
<body onload="startNFC()">
  <div class="box">
    <h2>✉ Send a Message</h2>
    <div class="nav">
      <a href="inbox.php" class="btn small">📥 Inbox</a>
      <a href="index.php" class="btn small danger">Logout</a>
    </div>

    <?php if($success) echo "<p class='message success'>$success</p>"; ?>
    <?php if($error) echo "<p class='message error'>$error</p>"; ?>

    <p id="status" class="status">Waiting for NFC scan...</p>

    <form method="POST" class="form">
      <input type="hidden" name="nfc_id" id="nfc_id" value="<?= $_SESSION["nfc_id"] ?>">
      <select name="receiver" required class="input">
        <option value="">Select recipient</option>
        <?php foreach ($users as $u): ?>
          <option value="<?= $u["id"] ?>" <?= $replyTo==$u["id"]?"selected":"" ?>>
            <?= htmlspecialchars($u["username"]) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <textarea name="message" rows="4" required placeholder="Type your message..."></textarea>
      <button type="submit" id="sendBtn" class="btn">Send</button>
    </form>
  </div>
</body>
</html>
