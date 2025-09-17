document.getElementById("nfcButton").addEventListener("click", async () => {
  try {
    const ndef = new NDEFReader();
    await ndef.scan();
    ndef.onreading = (event) => {
      const uid = event.serialNumber; // NFC UID
      document.getElementById("nfc_uid").value = uid;
      document.getElementById("nfcForm").submit();
    };
  } catch (err) {
    alert("NFC scan failed: " + err);
  }
});
