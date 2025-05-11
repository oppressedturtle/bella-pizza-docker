<?php
if ((isset($_SESSION["role"]) && $_SESSION["role"] === "admin") || isset($_SESSION["user_id"])): ?>
  <style>
    #chatbot-container {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: 9999;
      font-family: 'Segoe UI', sans-serif;
    }

    #chat-toggle-btn {
      background: #dc3545;
      color: #fff;
      padding: 16px 24px;
      font-size: 18px;
      border: none;
      border-radius: 50px;
      cursor: pointer;
      box-shadow: 0 4px 10px rgba(0,0,0,0.25);
      font-weight: bold;
      transition: background 0.3s, transform 0.2s;
    }

    #chat-toggle-btn:hover {
      background: #b82d3b;
      transform: scale(1.05);
    }

    #chatbox {
  display: none;
  background: #fff;
  width: 320px;
  height: 450px;
  border-radius: 16px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.3);
  margin-top: 10px;
  overflow: hidden;
  flex-direction: column;
}


    #chat-messages {
      flex-grow: 1;
      padding: 10px;
      overflow-y: auto;
      font-size: 14px;
    }

    #chat-input-section {
      display: flex;
      padding: 10px;
      border-top: 1px solid #eee;
    }

    #chat-input {
      flex-grow: 1;
      padding: 8px 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      margin-right: 8px;
    }

    #send-btn {
      background: #f8c102;
      border: none;
      padding: 8px 12px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
      color: #000;
      transition: background 0.3s;
    }

    #send-btn:hover {
      background: #e0ad00;
    }

    .user-msg {
      color: #dc3545;
      margin-bottom: 5px;
      font-weight: 500;
    }

    .bot-msg {
      color: #444;
      margin-bottom: 10px;
    }
  </style>

  <div id="chatbot-container">
    <button id="chat-toggle-btn" onclick="toggleChat()">Chat</button>

    <div id="chatbox">
      <div id="chat-messages"></div>
      <div id="chat-input-section">
        <input type="text" id="chat-input" placeholder="Ask me anything about your order..." />
        <button id="send-btn" onclick="sendMessage()">Send</button>
      </div>
    </div>
  </div>

  <script>
    let chatInitialized = false;

    function toggleChat() {
      const box = document.getElementById("chatbox");
      const isVisible = box.style.display === "flex";

      box.style.display = isVisible ? "none" : "flex";

      if (!chatInitialized && !isVisible) {
        showBotGreeting();
        chatInitialized = true;
      }
    }

    function showBotGreeting() {
      const messages = document.getElementById("chat-messages");
      const botMsg = document.createElement("div");
      botMsg.className = "bot-msg";
      botMsg.textContent = "Bot: Hi! Ask me about our pizzas, sides, or your order 🧀🍕";
      messages.appendChild(botMsg);
      messages.scrollTop = messages.scrollHeight;
    }

    function sendMessage() {
      const input = document.getElementById("chat-input");
      const message = input.value.trim();
      if (!message) return;

      const messages = document.getElementById("chat-messages");

      const userMsg = document.createElement("div");
      userMsg.className = "user-msg";
      userMsg.textContent = "You: " + message;
      messages.appendChild(userMsg);

      fetch('chatbot_backend.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'message=' + encodeURIComponent(message)
      })
      .then(res => res.json())
      .then(data => {
        const botMsg = document.createElement("div");
        botMsg.className = "bot-msg";
        botMsg.textContent = "Bot: " + data.reply;
        messages.appendChild(botMsg);
        messages.scrollTop = messages.scrollHeight;
      });

      input.value = '';
      messages.scrollTop = messages.scrollHeight;
    }
  </script>
<?php endif; ?>
