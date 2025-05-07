<?php
require_once "includes/session_config.php";
require_once "includes/csrf.php";

$host = "db";
$dbname = "RestaurantDB";
$username = "root";
$password = "rootpass";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

require_once "log_helper.php";

if (!isset($_SESSION["employee_id"]) || !in_array($_SESSION["role"], ['admin', 'support'])) {
    header("Location: login.php");
    exit();
}

$success = "";
$categories = $pdo->query("SELECT * FROM category ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    validate_csrf_token();

    $item_name   = trim($_POST['item_name']);
    $description = trim($_POST['description']);
    $price       = $_POST['price'];
    $category_id = $_POST['category_id'];
    $availability = isset($_POST['availability']) ? 1 : 0;

    $image_path = "";
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir  = "img/";
        $file_name   = uniqid() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;
        $ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (in_array($ext, ["jpg","jpeg","png","gif"]) && move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $file_name;
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO menu 
          (item_name, description, price, category_id, availability, image_path)
        VALUES 
          (:item_name, :description, :price, :category_id, :availability, :image_path)
    ");
    $stmt->execute([
        ':item_name'   => $item_name,
        ':description' => $description,
        ':price'       => $price,
        ':category_id' => $category_id,
        ':availability'=> $availability,
        ':image_path'  => $image_path
    ]);

    log_action(
        "Add Menu Item",
        "Menu item '$item_name' was added by employee {$_SESSION['username']}",
        "INFO",
        $_SESSION["employee_id"],
        null,
        $_SESSION["username"]
    );

    $success = "Menu item added successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Menu Item — Bella Pizza</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    :root {
      --primary: #dc3545;
      --accent: #f8c102;
      --light: #fff;
      --bg: #fff8f3
      ;
      --text: #333;
    }

    * { box-sizing: border-box; }

    body {
      margin: 0;
      background: var(--bg);
      font-family: 'Segoe UI', sans-serif;
      color: var(--text);
    }

    .navbar {
      position: sticky;
      top: 0;
      background: var(--primary);
      color: white;
      padding: 12px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
      z-index: 1000;
    }

    .navbar h1 {
      margin: 0;
      font-size: 20px;
    }

    .nav-links a {
      color: white;
      text-decoration: none;
      margin-left: 15px;
      font-weight: bold;
    }

    .nav-links a:hover {
      color: var(--accent);
    }

    .container {
      background: var(--light);
      max-width: 700px;
      margin: 40px auto;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: var(--primary);
    }

    .message {
      text-align: center;
      color: green;
      margin-bottom: 15px;
      font-weight: bold;
    }

    label {
      display: block;
      margin-bottom: 10px;
      font-weight: 500;
    }

    input[type="text"],
    input[type="number"],
    textarea,
    select {
      width: 100%;
      padding: 12px;
      margin-top: 5px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 1rem;
    }

    textarea {
      resize: vertical;
    }

    #drop-area {
      border: 2px dashed #ccc;
      border-radius: 6px;
      padding: 15px;
      text-align: center;
      margin-bottom: 20px;
      transition: background 0.3s, border-color 0.3s;
      cursor: pointer;
    }

    #drop-area.hover {
      border-color: var(--primary);
      background: #ffecec;
    }

    #preview {
      display: block;
      max-width: 100%;
      margin-top: 10px;
      border-radius: 6px;
    }

    input[type="checkbox"] {
      margin-right: 8px;
    }

    button {
      width: 100%;
      padding: 12px;
      background: var(--primary);
      color: var(--light);
      border: none;
      border-radius: 6px;
      font-size: 1rem;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s;
    }

    button:hover {
      background: #a71d2a;
    }

    .back-link {
      text-align: center;
      margin-top: 20px;
    }

    .back-link a {
      text-decoration: none;
      background: var(--accent);
      color: black;
      padding: 10px 16px;
      border-radius: 6px;
      font-weight: bold;
    }

    .back-link a:hover {
      background: #ddb400;
    }

    @media (max-width: 600px) {
      .container {
        margin: 20px;
        padding: 20px;
      }

      .navbar {
        flex-direction: column;
        align-items: flex-start;
      }

      .nav-links {
        margin-top: 10px;
      }
    }
  </style>
</head>
<body>

<div class="navbar">
  <h1>Bella Pizza</h1>
  <div class="nav-links">
    <a href="dashboard.php">Dashboard</a>
    <a href="edit_menu_items.php">Menu</a>
    <a href="logout.php">Logout</a>
  </div>
</div>

<div class="container">
  <h2>Add Menu Item</h2>

  <?php if ($success): ?>
    <div class="message"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <label>Item Name
      <input type="text" name="item_name" required>
    </label>

    <label>Description
      <textarea name="description" rows="4"></textarea>
    </label>

    <label>Price (BD)
      <input type="number" name="price" step="0.01" required>
    </label>

    <label>Category
      <select name="category_id" required>
        <option value="">— Select Category —</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= $c['category_id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>

    <label for="image">Upload Image (optional)</label>
    <div id="drop-area">
      <p>Drag & Drop or Click to Browse</p>
      <input type="file" id="image" name="image" accept="image/*" style="display:none;">
      <img id="preview" src="" alt="Preview" style="display:none;">
    </div>

    <label>
      <input type="checkbox" name="availability" checked> Available
    </label>

    <button type="submit">➕ Add Item</button>
  </form>

  <div class="back-link">
    <a href="edit_menu_items.php">← Back to Menu</a>
  </div>
</div>

<script>
  const dropArea = document.getElementById('drop-area');
  const fileInput = document.getElementById('image');
  const preview = document.getElementById('preview');

  dropArea.addEventListener('click', () => fileInput.click());
  ['dragover', 'dragleave', 'drop'].forEach(evt => {
    dropArea.addEventListener(evt, e => {
      e.preventDefault();
      dropArea.classList[evt === 'dragleave' ? 'remove' : 'add']('hover');
      if (evt === 'drop' && e.dataTransfer.files.length) {
        fileInput.files = e.dataTransfer.files;
        showPreview(e.dataTransfer.files[0]);
      }
    });
  });

  fileInput.addEventListener('change', () => {
    if (fileInput.files[0]) showPreview(fileInput.files[0]);
  });

  function showPreview(file) {
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  }
</script>

</body>
</html>
