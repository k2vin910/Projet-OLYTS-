<?php
session_start();

// Connect to MySQL
$bdd = new PDO("mysql:host=localhost;dbname=olyts;charset=utf8", "root", "");
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $nfc_id  = trim($_POST["nfc_id"] ?? "");
    $password = $_POST["password"] ?? "";

    // Fetch user by username
    $stmt = $bdd->prepare("SELECT id, username, nfc_id, is_admin, password FROM identite WHERE username = :u");
    $stmt->execute([":u" => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $error = "❌ Username not recognized.";
    } else {
        if ($user["is_admin"]) {
            // Admin login with password
            if (!$password || !password_verify($password, $user["password"])) {
                $error = "❌ Incorrect admin password.";
            } else {
                $_SESSION["identite"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                header("Location: bind.php");
                exit;
            }
        } else {
            // Regular user login via NFC
            if (!$nfc_id || $nfc_id !== $user["nfc_id"]) {
                $error = "❌ NFC tag invalid or missing.";
            } else {
                $_SESSION["identite"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["nfc_id"] = $user["nfc_id"];
                header("Location: inbox.php");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Secret Messages - Login</title>
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
        document.getElementById("status").textContent = "📡 Scanning for NFC tag...";
        ndef.onreading = event => {
            const uid = event.serialNumber;
            document.getElementById("nfc_id").value = uid;
            document.getElementById("status").textContent = "✅ Tag detected: " + uid;
        };
    } catch(err) {
        alert("❌ NFC scan failed: " + err);
    }
}
</script>
</head>
<body>
<div class="box">
    <h2>🔐 Secret Messages</h2>

    <?php if($error) echo "<p class='message error'>$error</p>"; ?>

    <form method="POST" class="form">
        <input type="text" name="username" placeholder="Username" required class="input">
        <input type="password" name="password" placeholder="Admin Password (for admins only)" class="input">
        <input type="hidden" name="nfc_id" id="nfc_id">
        <p id="status" class="status">Admins use password. Regular users scan NFC.</p>

        <button type="button" class="btn" onclick="scanNFC()">Scan NFC (Users)</button>
        <button type="submit" class="btn">Login</button>
    </form>
</div>
</body>
</html>
