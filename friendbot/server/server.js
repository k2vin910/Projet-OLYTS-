// server/server.js
const express = require("express");
const fetch = require("node-fetch");
const path = require("path");
require("dotenv").config();

const app = express();
app.use(express.json()); // parse JSON body

const publicPath = path.join(__dirname, "public"); // <-- change if your public folder is elsewhere
app.use(express.static(publicPath)); // serve index.html + assets

// API route
app.post("/api/chat", async (req, res) => {
  const { message } = req.body;
  console.log("Incoming message:", message);

  try {
    const response = await fetch("https://api.openai.com/v1/chat/completions", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Authorization": `Bearer ${process.env.OPENAI_API_KEY}`
      },
      body: JSON.stringify({
        model: "gpt-4o-mini",
        messages: [
          { role: "system", content: "You are FriendBot, a friendly but somewhat serious chatbot." },
          { role: "user", content: message }
        ]
      })
    });

    const data = await response.json();
    console.log("OpenAI raw response:", data);
    if (data.error) return res.status(400).json({ error: data.error });
    const reply = data.choices?.[0]?.message?.content ?? "No reply from model";
    res.json({ reply });
  } catch (err) {
    console.error("Server error calling OpenAI:", err);
    res.status(500).json({ error: { message: "Internal server error" } });
  }
});

// catch-all: serve index.html for any other path
// ❌ old (breaks in Express 5)
// app.get("*", (req, res) => {

// ✅ new (works in Express 5+)
app.get("/*", (req, res) => {
  res.sendFile(path.join(publicPath, "index.html"));
});

