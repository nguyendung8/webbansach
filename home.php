<?php

   include 'config.php';

   session_start();

   $user_id = $_SESSION['user_id']; //tạo session người dùng thường

   if(!isset($user_id)){// session không tồn tại => quay lại trang đăng nhập
      header('location:login.php');
   }

   if(isset($_POST['add_to_cart'])){//thêm sách vào giỏi hàng từ form submit name='add_to_cart'

      $product_name = $_POST['product_name'];
      $product_price = $_POST['product_price'];
      $product_image = $_POST['product_image'];
      $product_quantity = $_POST['product_quantity'];

      if($product_quantity==0){
         $message[] = 'Truyện đã hết hàng!';
      }
      else{
         $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

         if(mysqli_num_rows($check_cart_numbers) > 0){//kiểm tra sách có trong giỏ hàng chưa và tăng số lượng
            $result=mysqli_fetch_assoc($check_cart_numbers);
            $num=$result['quantity']+$product_quantity;
            $select_quantity = mysqli_query($conn, "SELECT * FROM `products` WHERE name='$product_name'");
            $fetch_quantity = mysqli_fetch_assoc($select_quantity);
            if($num>$fetch_quantity['quantity']){
               $num=$fetch_quantity['quantity'];
            }
            mysqli_query($conn, "UPDATE `cart` SET quantity='$num' WHERE name = '$product_name' AND user_id = '$user_id'");
            $message[] = 'Truyện đã có trong giỏ hàng và được thêm số lượng!';
         }else{
            mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image) VALUES('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('query failed');
            $message[] = 'Truyện đã được thêm vào giỏ hàng!';
         }
      }
   }

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Trang chủ</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="home">

   <div class="content">
      <h3>Mỗi ngày một quyển truyện.</h3>
      <p>Những quyển truyện đều mang trong mình những bài học ý nghĩa, những trải nghiệm đáng giá.</p>
      <a href="about.php" class="white-btn">Khám phá thêm</a>
   </div>

</section>

<section class="products">

   <h1 class="title">Truyện mới nhất</h1>

   <div class="box-container">

      <?php  
         $select_products = mysqli_query($conn, "SELECT * FROM `products` ORDER BY id DESC  LIMIT 6") or die('query failed');
         if(mysqli_num_rows($select_products) > 0){
            while($fetch_products = mysqli_fetch_assoc($select_products)){
      ?>
               <form action="" method="post" class="box">
                  <img class="image" src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
                  <div class="name"><?php echo $fetch_products['name']; ?> (<?php echo $fetch_products['category']; ?>)</div>
                  <div class="name"><?php echo $fetch_products['describes']; ?></div>
                  <div class="price"><?php echo number_format($fetch_products['newprice'],0,',','.' ); ?>/<span style="text-decoration-line:line-through"><?php echo number_format($fetch_products['price'],0,',','.' ); ?></span> VND (<?php echo $fetch_products['discount']; ?>% SL: <?php echo $fetch_products['quantity']; ?>)</div>
                  <span style="font-size: 17px; display: flex;">Số lượng mua:</span>
                  <input type="number" min="<?=($fetch_products['quantity']>0) ? 1:0 ?>" max="<?php echo $fetch_products['quantity']; ?>" name="product_quantity" value="<?=($fetch_products['quantity']>0) ? 1:0 ?>" class="qty">
                  <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
                  <input type="hidden" name="product_category" value="<?php echo $fetch_products['category']; ?>">
                  <input type="hidden" name="product_price" value="<?php echo $fetch_products['newprice']; ?>">
                  <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
                  <input type="submit" value="Thêm vào giỏ hàng" name="add_to_cart" class="btn">
               </form>
      <?php
            }
         }else{
            echo '<p class="empty">Chưa có truyện được bán!</p>';
         }
      ?>
   </div>

   <div class="load-more" style="margin-top: 2rem; text-align:center">
      <a href="shop.php" class="option-btn">Xem thêm</a>
   </div>

</section>

<section class="about">

   <div class="flex">

      <div class="image">
         <img src="images/about-img.jpg" alt="">
      </div>

      <div class="content">
         <h3>Comic</h3>
         <p>Từ hội những bạn trẻ yêu thích đọc truyện, chúng mình muốn cùng chia sẻ những đam mê và sở thích tới mọi người.</p>
      </div>

   </div>

</section>

<section class="home-contact">

   <div class="content">
      <h3>Bạn có thắc mắc?</h3>
      <p>Hãy để lại những điều bạn còn thắc mắc, băn khoăn hay muốn chia sẻ thêm về những quyển truyện cho chúng mình tại đây để chúng mình có thể giải đáp giúp bạn</p>
      <a href="contact.php" class="white-btn">Liên hệ</a>
   </div>

</section>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>