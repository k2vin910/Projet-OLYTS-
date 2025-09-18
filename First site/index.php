<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Accueil - OLYTS</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    df-messenger {
      --df-messenger-bot-message: #f0f0f0;
      --df-messenger-button-titlebar-color: #ff6600;
      --df-messenger-font-color: #333;
      --df-messenger-chat-background-color: #ffffff;
    }
  </style>
</head>
<body>
  <!-- En-tête -->
  <header>
    <h1>OLYTS</h1>
    <nav>
      <a href="index.php">Accueil</a>
      <a href="shop.html">Boutique</a>
      <a href="contact.html">Contact</a>
    </nav>
  </header>

  <!-- Contenu principal -->
  <main>
    <section class="hero">
      <h2>Bienvenue</h2>
      <p>Découvrez nos produits et discutez avec notre chatbot intelligent.</p>
      <a href="shop.html" class="btn">Découvrir la boutique</a>
    </section>

   

    <!-- Chatbot -->
    <df-messenger
      intent="WELCOME"
      chat-title="CHATBOT"
      agent-id="53b87d90-76a9-48c0-b325-b1480aed1197"
      language-code="fr"
    ></df-messenger>
  </main>

  <!-- Pied de page -->
  <footer>
    © <span id="year"></span>  - Tous droits réservés
  </footer>

  <!-- Script Chatbot -->
  <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
  <script>
    const pageRedirection = "https://projet-olyts.ct.ws/";

    window.addEventListener('df-response-received', function(event) {
      const response = event.detail.response.queryResult.fulfillmentText;
      if (response && response.toLowerCase().includes("redirect_code123")) {
        const dfMessenger = document.querySelector('df-messenger');
        dfMessenger.renderCustomText("Code valide ! Tu vas être redirigé...");
        setTimeout(() => {
          window.location.href = pageRedirection;
        }, 1500);
      }
    });

    document.getElementById("year").textContent = new Date().getFullYear();
  </script>
</body>
</html>
