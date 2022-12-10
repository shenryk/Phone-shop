<?php

$pdo = new PDO('mysql:host=localhost;port=3306;dbname=products_crud', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id =$_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit;

}

$statement = $pdo->prepare('SELECT * FROM products WHERE id =:id');
$statement->bindValue(':id',$id);
$statement->execute();
$product = $statement->fetch(PDO::FETCH_ASSOC);


// echo '<pre>';
// var_dump($product);
// echo '</pre>';
// exit;

// echo $_SERVER['REQUEST_METHOD'].'<br>';
$errors = [];

$title = $product['title'];
$price = $product['price'];
$description = $product['description'];

function randomString($n)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $str = '';
    for ($i = 0; $i < $n; ++$i) {
        $index = rand(0, strlen($characters) - 1);
        $str .= $characters[$index];
    }

    return $str;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    

    if (!$title) {
        $errors[] = 'Product title is required';
    }

    if (!$price) {
        $errors[] = 'Product price is required';
    }
    if (!is_dir('images')) {
        mkdir('images');
    }

    if (empty($errors)) {
        $image = $_FILES['image'] ?? null;
        $imagePath =$product['image'];

        
        if ($image && $image['tmp_name']) {

            if($product['image']){
                unlink($product['image']);
            }
    

            $imagePath = 'images/'.randomString(8).'/'.$image['name'];
            mkdir(dirname($imagePath));
            
            move_uploaded_file($image['tmp_name'], $imagePath);
        }
        

        $statement = $pdo->prepare("UPDATE  products SET title =:title, image=:image,description=:description,price=:price WHERE id=:id");
                // VALUEs (:title, :image, :description, :price , :date);
        $statement->bindValue(':title', $title);
        $statement->bindValue(':image', $imagePath);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':price', $price);
        $statement->bindValue(':id', $id);
        
        $statement->execute();
        header('Location:index.php');

    }
}

?>

<!doctype html>
<html lang="eng" dir="ltr">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=0">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.rtl.min.css" integrity="sha384-+4j30LffJ4tgIMrq9CwHvn0NjEvmuDCOfk6Rpg2xg7zgOxWWtLtozDEEVvBPgHqE" crossorigin="anonymous">
    <link rel = "stylesheet" href="app.css">
    <title>Create new Product</title>
  </head>
  <body>
    <p>
        <a href="index.php" class="btn btn-secondary">Go back to Products</a>
    </p>

    <h1>Update Product <b> <?php echo $product['title']?></b></h1>
    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $error):?>
        <div><?php echo $error; ?></div>      
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
        
        <?php if ($product['image']): ?>
            <img src="<?php echo $product['image']?>" style="width:120px;">
        <?php endif; ?>

    <div class ="form-group">
        <label>Product Image</label>
        <br>
        <input type = "file" name="image">
      </div>
      <div class ="form-group">
        <label>Product Title</label>
        <input type = "text" name="title" class = "form-control" value="<?php echo $title; ?>">
      </div>
      <div class ="form-group">
        <label>Product Description</label>
        <textarea  class = "form-control" name="description"><?php echo $description; ?></textarea>
      </div>
      <div class ="form-group">
        <label>Product Price</label>
        <input type = "number" name="price" step=".01" class = "form-control" value="<?php echo $price; ?>">
      </div>
      <button type="submit" class="btn btn-primary">Submit</button>
    </form>
  </body>
</html>


