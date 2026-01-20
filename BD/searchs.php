<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Website Search</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f9f9f9;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .search-box {
      background: white;
      padding: 20px 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .search-box input[type="text"] {
      padding: 10px;
      width: 250px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .search-box input[type="submit"] {
      padding: 10px 15px;
      background: #007BFF;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      margin-left: 10px;
    }
    .search-box input[type="submit"]:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>

  <form class="search-box" action="search.php" method="GET">
    <input type="text" name="query" placeholder="Search by name..." required>
    <input type="submit" value="Search">
  </form>

</body>
</html>
