document.getElementById("nfcButton").addEventListener("click", async () => {
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
      document.getElementById("nfcForm").submit();
    };
  } catch (err) {
    alert("❌ NFC scan failed: " + err);
  }
});
