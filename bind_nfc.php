<?php
session_start();

$bdd = new PDO("mysql:host=sql113.infinityfree.com;dbname=if0_39961547_olyts;charset=utf8", "if0_39961547", "GnBN8eTGaQ6hcD");
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// --- Admin login check ---
if (!isset($_SESSION["identite"])) {
    die("❌ Access denied. Please log in as admin.");
}

// Fetch user info from DB
$stmt = $bdd->prepare("SELECT username, is_admin FROM identite WHERE id = :id");
$stmt->execute([":id" => $_SESSION["identite"]]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check admin flag
if (!$user || !$user["is_admin"]) {
    die("❌ Access denied. Admin only.");
}

// --- Binding logic ---
$error = null;
$success = null;

// Fetch all users
$users = $bdd->query("SELECT id, username, nfc_id FROM identite")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = $_POST["user"] ?? null;
    $nfcId = trim($_POST["nfc_id"] ?? "");

    if (!$userId || !$nfcId) {
        $error = "❌ Select a user and scan an NFC tag.";
    } else {
        try {
            $stmt = $bdd->prepare("UPDATE identite SET nfc_id = :nid WHERE id = :uid");
            $stmt->execute([":nid" => $nfcId, ":uid" => $userId]);
            $success = "✅ NFC tag successfully bound to user!";

            // Refresh users
            $users = $bdd->query("SELECT id, username, nfc_id FROM identite")->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error = "❌ Failed to bind NFC tag: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bind NFC Tag</title>
    <link rel="stylesheet" href="style.css">
    <script>
    async function scanNFC() {
        if (!("NDEFReader" in window)) {
            alert("⚠️ NFC not supported on this device/browser.");
            return;
        }
        try {
            const ndef = new NDEFReader();
            await ndef.scan();
            alert("📡 Ready to scan NFC tag...");
            ndef.onreading = event => {
                const uid = event.serialNumber;
                document.getElementById("nfc_uid").value = uid;
                document.getElementById("status").textContent = "✅ Tag detected: " + uid;
            };
        } catch (err) {
            alert("❌ NFC scan failed: " + err);
        }
    }
    </script>
</head>
<body>
<div class="box">
    <h2>🔗 Bind NFC Tag to User</h2>

    <?php if($error) echo "<p class='message error'>$error</p>"; ?>
    <?php if($success) echo "<p class='message success'>$success</p>"; ?>

    <form method="POST">
        <label>Select user:</label>
        <select name="user" class="input" required>
            <option value="">-- Select a user --</option>
            <?php foreach($users as $u): ?>
                <option value="<?= $u['id'] ?>">
                    <?= htmlspecialchars($u['username']) ?> <?= $u['nfc_id'] ? "(Already: {$u['nfc_id']})" : "" ?>
                </option>
            <?php endforeach; ?>
        </select>

        <p id="status" class="status">Click "Scan NFC" and place tag near device</p>
        <input type="hidden" name="nfc_id" id="nfc_uid">

        <button type="button" class="btn" onclick="scanNFC()">Scan NFC</button>
        <button type="submit" class="btn">Bind Tag</button>
    </form>
</div>
</body>
</html>
