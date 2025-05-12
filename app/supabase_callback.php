<?php
require_once 'includes/session_config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Google Login Redirect - Bella Pizza</title>
  <style>
    body {
      font-family: sans-serif;
      background: #fff8f3;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      flex-direction: column;
    }
  </style>
</head>
<body>
  <h2>Signing you in...</h2>

  <script type="module">
    import { createClient } from 'https://cdn.jsdelivr.net/npm/@supabase/supabase-js/+esm';

    const supabase = createClient(
      'https://fpvkzzsoudxcsbejxvow.supabase.co',
      'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImZwdmt6enNvdWR4Y3NiZWp4dm93Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDY5ODkyNzIsImV4cCI6MjA2MjU2NTI3Mn0.aCydxiEiXHxyOyY3TTNHeuNL3vGALWKpAbnIC4Jjy7s'
    );

    const { data: { user }, error } = await supabase.auth.getUser();

    if (user) {
      const res = await fetch("login_handler.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(user)
      });

      if (res.ok) {
        window.location.href = "dashboard_user.php";
      } else {
        document.body.innerHTML = "<h2>Login failed on server</h2>";
      }
    } else {
      document.body.innerHTML = "<h2>Could not get user</h2>";
    }
  </script>
</body>
</html>
