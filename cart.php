<?php
include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

if(isset($_POST['update_cart'])){
   $cart_id = $_POST['cart_id'];
   $cart_quantity = $_POST['cart_quantity'];
   mysqli_query($conn, "UPDATE `cart` SET quantity = '$cart_quantity' WHERE id = '$cart_id'") or die('query failed');
   $message[] = 'cart quantity updated!';
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$delete_id'") or die('query failed');
   header('location:cart.php');
}

if(isset($_GET['delete_all'])){
   mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
   header('location:cart.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>cart</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>shopping cart</h3>
   <p> <a href="home.php">home</a> / cart </p>
</div>

<section class="shopping-cart">

   <h1 class="title">products added</h1>

   <div class="box-container">
      <?php
         $grand_total = 0;
         $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
         if(mysqli_num_rows($select_cart) > 0){
            while($fetch_cart = mysqli_fetch_assoc($select_cart)){   
      ?>
      <div class="box">
         <a href="cart.php?delete=<?php echo $fetch_cart['id']; ?>" class="fas fa-times" onclick="return confirm('delete this from cart?');"></a>
         <img src="uploaded_img/<?php echo $fetch_cart['image']; ?>" alt="">
         <div class="name"><?php echo $fetch_cart['name']; ?></div>
         <div class="category"><?php echo $fetch_cart['category']; ?></div>
         <div class="price">Rs.<?php echo $fetch_cart['price']; ?>/-</div>
         <form action="" method="post">
            <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id']; ?>">
            <input type="number" min="1" name="cart_quantity" value="<?php echo $fetch_cart['quantity']; ?>">
            <input type="submit" name="update_cart" value="update" class="option-btn">
         </form>
         <div class="sub-total"> sub total : <span>Rs.<?php echo $sub_total = ($fetch_cart['quantity'] * $fetch_cart['price']); ?>/-</span> </div>
      </div>
      <?php
      $grand_total += $sub_total;
         }
      }else{
         echo '<p class="empty">your cart is empty</p>';
      }
      ?>
   </div>

   <div style="margin-top: 2rem; text-align:center;">
      <a href="cart.php?delete_all" class="delete-btn <?php echo ($grand_total > 1)?'':'disabled'; ?>" onclick="return confirm('delete all from cart?');">delete all</a>
   </div>

   <div class="cart-total">
      <p>grand total : <span>Rs.<?php echo $grand_total; ?>/-</span></p>
      <div class="flex">
         <a href="shop.php" class="option-btn">continue shopping</a>
         <a href="checkout.php" class="btn <?php echo ($grand_total > 1)?'':'disabled'; ?>">proceed to checkout</a>
      </div>
   </div>

</section>

<!-- Collaborative Filtering Recommendations -->
<section class="recommended-books">
   <h2>Recommended Books</h2>
   <?php
   // Assume user_id is already set and retrieved from session
   $selected_user_id = $user_id;

   // Retrieve user's interactions with books
   $user_interactions = array();
   $interactions_query = "SELECT book_id FROM `user_interactions` WHERE user_id = '$selected_user_id'";
   $interactions_result = mysqli_query($conn, $interactions_query);
   while ($row = mysqli_fetch_assoc($interactions_result)) {
       $user_interactions[] = $row['book_id'];
   }

   // Calculate similarity and recommend books
   $recommendations_query = "SELECT DISTINCT book_id FROM `user_interactions` WHERE user_id != '$selected_user_id'";
   $recommendations_result = mysqli_query($conn, $recommendations_query);
   
   $recommended_books = array();
   while ($row = mysqli_fetch_assoc($recommendations_result)) {
       $book_id = $row['book_id'];
       
       // Calculate similarity between users' interactions
       $common_interactions = array_intersect($user_interactions, getUserInteractions($book_id));
       $similarity = count($common_interactions) / sqrt(count($user_interactions) * count(getUserInteractions($book_id)));
       
       if ($similarity > 0) {
           $recommended_books[] = array('book_id' => $book_id, 'similarity' => $similarity);
       }
   }

   // Sort recommended_books array by similarity in descending order
   usort($recommended_books, function($a, $b) {
       return $b['similarity'] - $a['similarity'];
   });

   // Display the top recommended books
   foreach ($recommended_books as $recommended_book) {
       $book_id = $recommended_book['book_id'];
       $book_query = "SELECT * FROM `books` WHERE id = '$book_id'";
       $book_result = mysqli_query($conn, $book_query);
       $book_data = mysqli_fetch_assoc($book_result);

       // Display book details
       echo '<div class="recommended-book">';
       echo '<img src="uploaded_img/' . $book_data['image'] . '" alt="' . $book_data['name'] . '">';
       echo '<h3>' . $book_data['name'] . '</h3>';
       echo '<p>' . $book_data['author'] . '</p>';
       echo '<p>' . $book_data['category'] . '</p>';
       echo '<p>Price: $' . $book_data['price'] . '/-</p>';
       echo '</div>';
   }
   ?>
</section>
<!-- Collaborative Filtering Recommendations -->

<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

