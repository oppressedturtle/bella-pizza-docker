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
$edit_item = null;

$category_stmt = $pdo->query("SELECT * FROM category");
$categories = $category_stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['id'])) {
    $menu_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM menu WHERE menu_id = ?");
    $stmt->execute([$menu_id]);
    $edit_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$edit_item) {
        die("Item not found.");
    }
} else {
    die("No item ID specified.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    validate_csrf_token();

    $item_name   = $_POST['item_name'];
    $description = $_POST['description'];
    $price       = $_POST['price'];
    $category_id = $_POST['category_id'];
    $availability = isset($_POST['availability']) ? 1 : 0;
    $image_path = $edit_item['image_path'] ?? "";

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "img/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . uniqid() . "_" . $image_name;
        $ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (in_array($ext, ["jpg", "jpeg", "png", "gif"])) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_path = basename($target_file);
            }
        }
    }

    $stmt = $pdo->prepare("UPDATE menu SET item_name = :item_name, description = :description, price = :price, category_id = :category_id, availability = :availability, image_path = :image_path WHERE menu_id = :menu_id");
    $stmt->execute([
        ':item_name'   => $item_name,
        ':description' => $description,
        ':price'       => $price,
        ':category_id' => $category_id,
        ':availability'=> $availability,
        ':image_path'  => $image_path,
        ':menu_id'     => $menu_id
    ]);

    log_action("Edit Menu Item", "Updated item '$item_name' (ID: $menu_id)", "INFO", $_SESSION["employee_id"], null, $_SESSION["username"]);

    header("Location: edit_menu_item.php?id=$menu_id&success=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Menu Item</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #dc3545;
      --accent: #f8c102;
      --light: #fff;
      --bg: #fff8f3;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Fredoka', sans-serif;
      background: var(--bg);
      background-size: cover;
      color: #333;
    }

    .navbar {
      position: sticky;
      top: 0;
      background: var(--primary);
      color: white;
      padding: 14px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      z-index: 1000;
      box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }

    .navbar h1 {
      margin: 0;
      font-size: 22px;
      letter-spacing: 0.5px;
    }

    .nav-links {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
    }

    .nav-links a {
      color: white;
      text-decoration: none;
      font-weight: 600;
      padding: 8px 12px;
      border-radius: 6px;
      transition: background 0.2s;
    }

    .nav-links a:hover {
      background: rgba(255,255,255,0.15);
    }

    .container {
      max-width: 700px;
      margin: 40px auto;
      background: var(--light);
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: var(--primary);
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
    }

    input[type="text"],
    input[type="number"],
    textarea,
    select {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    input[type="checkbox"] {
      margin-right: 10px;
    }

    textarea { resize: vertical; }

    #drop-area {
      border: 2px dashed #ccc;
      padding: 20px;
      text-align: center;
      border-radius: 10px;
      margin-bottom: 20px;
      transition: 0.3s ease;
      cursor: pointer;
    }

    #drop-area.hover {
      border-color: var(--primary);
      background-color: #ffecec;
    }

    #preview {
      margin-top: 15px;
      max-width: 100%;
      border-radius: 8px;
      display: <?= $edit_item && $edit_item['image_path'] ? 'block' : 'none' ?>;
    }

    button {
      background-color: var(--primary);
      color: white;
      border: none;
      padding: 12px 18px;
      border-radius: 6px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.3s ease;
      width: 100%;
    }

    button:hover {
      background-color: #a71d2a;
    }

    .success {
      text-align: center;
      color: green;
      font-weight: bold;
      margin-bottom: 15px;
    }

    .back-link {
      text-align: center;
      margin-top: 25px;
    }

    .back-link a {
      text-decoration: none;
      background-color: var(--accent);
      color: black;
      padding: 10px 16px;
      border-radius: 6px;
      font-weight: bold;
      display: inline-block;
    }

    .back-link a:hover {
      background-color: #ddb400;
    }

    @media (max-width: 600px) {
      .navbar {
        flex-direction: column;
        align-items: flex-start;
      }

      .nav-links {
        margin-top: 10px;
        width: 100%;
        flex-direction: column;
      }

      .container {
        margin: 20px;
        padding: 20px;
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
  <h2>Edit Menu Item</h2>

  <?php if (isset($_GET['success'])): ?>
    <div class="success">Menu item updated successfully!</div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <label>Item Name:
      <input type="text" name="item_name" required value="<?= htmlspecialchars($edit_item['item_name']) ?>">
    </label>

    <label>Description:
      <textarea name="description" rows="4"><?= htmlspecialchars($edit_item['description']) ?></textarea>
    </label>

    <label>Price:
      <input type="number" step="0.01" name="price" required value="<?= htmlspecialchars($edit_item['price']) ?>">
    </label>

    <label>Category:
      <select name="category_id" required>
        <option value="">-- Select Category --</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat['category_id'] ?>" <?= ($edit_item['category_id'] == $cat['category_id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <label for="image">Upload New Image (optional):</label>
    <div id="drop-area">
      <p>Drag & Drop Image Here or Click to Browse</p>
      <input type="file" id="image" name="image" accept="image/*" style="display: none;">
      <?php if (!empty($edit_item['image_path'])): ?>
        <img id="preview" src="img/<?= htmlspecialchars($edit_item['image_path']) ?>" alt="Image Preview">
      <?php else: ?>
        <img id="preview" style="display: none;">
      <?php endif; ?>
    </div>

    <label>
      <input type="checkbox" name="availability" <?= $edit_item['availability'] ? 'checked' : '' ?>> Available
    </label>

    <button type="submit">Update Item</button>
  </form>

  <div class="back-link">
    <a href="edit_menu_items.php">← Back to Menu</a>
  </div>
</div>

<script>
const dropArea = document.getElementById("drop-area");
const input = document.getElementById("image");
const preview = document.getElementById("preview");

dropArea.addEventListener("click", () => input.click());
dropArea.addEventListener("dragover", (e) => {
    e.preventDefault();
    dropArea.classList.add("hover");
});
dropArea.addEventListener("dragleave", () => {
    dropArea.classList.remove("hover");
});
dropArea.addEventListener("drop", (e) => {
    e.preventDefault();
    dropArea.classList.remove("hover");
    if (e.dataTransfer.files.length > 0) {
        input.files = e.dataTransfer.files;
        showPreview(input.files[0]);
    }
});
input.addEventListener("change", () => {
    if (input.files.length > 0) {
        showPreview(input.files[0]);
    }
});

function showPreview(file) {
    const reader = new FileReader();
    reader.onload = function (e) {
        preview.src = e.target.result;
        preview.style.display = "block";
    };
    reader.readAsDataURL(file);
}
</script>

</body>
</html>
