<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoSpeedy-Home</title>
    <?php require('inc/links.php')?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>
 <style>
        .availability-form{
          margin-top: -50px;
          z-index: 2;
          position: relative;
        }
        @media screen and (max-width:575px){
          .availability-form{
            margin-top: 25px;  
            padding: 0 35px;
          }
        }
</style>

</head>


<body>
<?php
require('inc/header.php');


?>


<!-- slider -->
<div class="container-fluid px-lg-4 mt-4">
  <div class="swiper mySwiper">
    <div class="swiper-wrapper ">
      <div class="swiper-slide"><img src="images/slider/1.jpg" class="w-100 d-block" /></div>
      <div class="swiper-slide"><img src="images/slider/2.jpg"class="w-100 d-block" /></div>
    </div>
   
  </div>
</div>
<!-- slider end -->

<!-- availabilty form -->
<!-- <div class="container availability-form ">
  <div class="row">
    <div class="col-lg-12 bg-light shadow p-4 rounded">
      <h5 class="mb-4"><b>Check Availability<b></h5>
      <form>
        <div class="row align-items-end">
          <div class="col-lg-5 mb-3">
            <label class="form-label" style="font-weight: 500;"><b>Booking Date</b></label>
            <input type="date" class="form-control shadow-none">
          </div>
          <div class="col-lg-5 mb-3">
            <label class="form-label" style="font-weight: 500;"><b>Return date</b></label>
            <input type="date" class="form-control shadow-none">
          </div>
          <div class="col-lg-2  mt-2 mb-lg-3">
            <button type="submit" class="btn text-light  shadow-none custom-bg ">Check Availability</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div> -->
<!-- availabilty form end -->

<!-- jewellery -->

<h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">Vehicle</h2>  


<div class="container text-center">
  <div class="row">

  <?php
        $jewellery_res = select("SELECT * FROM `jewellery` WHERE  `status`=? AND `removed`=? ORDER BY `id` DESC LIMIT 3",[1,0],'ii');

        while($jewellery_data = mysqli_fetch_assoc($jewellery_res))
        {
          // get category of jewellery

          $cat_q = mysqli_query($conn,"SELECT c.name FROM `category` c INNER JOIN `jewellery_category` jcat ON c.id = jcat.category_id WHERE jcat.jewellery_id = '$jewellery_data[id]'");

          $category_data = "";
          while($cat_row = mysqli_fetch_assoc($cat_q)){
            $category_data .="<span class='badge rounded-pill bg-light text-dark text-wrap'>$cat_row[name]</span>";
          }

          // get thumbnail of jewellery

          $jewellery_thumb = JEWELLERY_IMG_PATH."thumbnail.jpg";
          $thumb_q = mysqli_query($conn,"SELECT * FROM `jewellery_image` WHERE `jewellery_id`='$jewellery_data[id]' AND `thumb`='1'");


          if(mysqli_num_rows($thumb_q)>0){
            $thumb_res = mysqli_fetch_assoc($thumb_q);
            $jewellery_thumb = JEWELLERY_IMG_PATH.$thumb_res['image'];
          }

          $book_btn = "";
          if(!$settings_r['shutdown'])
          {
            $login = 0;
            if(isset($_SESSION['login']) && $_SESSION['login'] == true){
              $login = 1;
            }
            $book_btn = "<button onclick='checkLogin($login,$jewellery_data[id])' class='bg-dark btn text-light'>Book Now</button>";
          }

          $rating_q = "SELECT AVG(rating) AS `avg_rating` FROM `rating` WHERE `jewellery_id`= $jewellery_data[id] ORDER BY `sr_no` DESC LIMIT 20";
          $rating_res = mysqli_query($conn,$rating_q);
          $rating_fetch = mysqli_fetch_assoc($rating_res);

          $rating_data = "";

          if($rating_fetch['avg_rating']!=NULL)
          {
            $rating_data = "<div class='rating mb-4'>
            <h6 class='mb-1'><b>Rating</b></h6>
            <span class='badge rounded-pill bg-light'>";

            for($i=0; $i<$rating_fetch['avg_rating']; $i++){
              $rating_data .="  <i class='bi bi-star-fill text-warning'></i>";
            }

            $rating_data .="</span>
            </div>";
          }


          // print jewellery card

          echo <<<data


          <div class="col-lg-4 col-md-6 my-3">
            <div class="card border-0 shadow" style="max-width: 350px; margin: auto;">
              <img src="$jewellery_thumb " class="card-img-top">
                  <div class="card-body">
                    <h5><b>$jewellery_data[name]</b> </h5>
                    <h6><b>Rent</b>-₹$jewellery_data[price]/- per day</h6>
                    <h6 class="mb-4"><b>Security Charges</b>-₹$jewellery_data[security_charge]/-</h6>
                    $rating_data
                    $book_btn
                    <a href="jewellery_details.php?id=$jewellery_data[id]" class="bg-dark btn text-light">View Details</a>
      
                  </div>
            </div>
          </div>


          


          data;

        }
      ?>
    <div class="col-lg-12 text-center mt-5">
      <a href="jewellery.php" class="btn text-light  shadow-none custom-bg"> Show more >>></a>
    </div>
  </div>
</div>
<!-- jewellery end -->

<!-- testimonials -->
<h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">Testimonials</h2>  

<!-- <div class="container mt-5"> -->
  <div class="swiper swiper-testimonial">
    <div class="swiper-wrapper mb-5">
      <?php

        $review_q = "SELECT r.*,uc.name AS uname, j.name AS jname FROM `rating` r 
        INNER JOIN `user_cred` uc ON r.user_id = uc.id
        INNER JOIN `jewellery` j ON r.jewellery_id = j.id ORDER BY `sr_no` ASC LIMIT 6";

        $review_res = mysqli_query($conn,$review_q);

        if(mysqli_num_rows($review_res)==0){
          echo 'no reviews yet!';
        }else{
          while($row = mysqli_fetch_assoc($review_res))
          {
            $star = "<i class='bi bi-star-fill text-warning'></i> ";
            for($i=1; $i<$row['rating']; $i++){
              $star .= " <i class='bi bi-star-fill text-warning'></i>";
            }
            echo<<<slides
            <div class="swiper-slide bg-dark text-light shadow p-4">
              <div class="profile d-flex align-items-center mb-3">
              <i class="bi bi-person-fill"></i>
                <h6 class="m-0 ms-2"><b>$row[uname]</b></h6>
              </div>
                <p>$row[review]</p>
               <div class="rating">
                $star
        
              </div>
            </div>

            slides;
          }
        }


      ?>
      
      
  </div>
</div>
<div class="col-lg-12 text-center mt-5">
      <a href="#" class="btn text-light  shadow-none custom-bg"> Show more >>></a>
</div>
<!-- testimonials end -->

<!-- contact us -->




<h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">Contact Us</h2>  
<div class="container">
  <div class="row">
    <div class="col-lg-8 col-md-8 p-4 mb-lg-0 mb-3 bg-light">
    <iframe class="w-100" src="<?php echo $contact_r['iframe'] ?>"  height="420px"  loading="lazy"></iframe>
    </div>
    <div class="col-lg-4 col-md-4">
      <div class="bg-light p-4 mb-4 shadow">
        <h5><b>Call Us</b></h5>
        <a href="tel: +<?php echo $contact_r['pn1'] ?>" class="d-inline-block mb-2 text-decoration-none text-dark" >
          <i class="bi bi-telephone-outbound-fill"></i>&nbsp;&nbsp;+<?php echo $contact_r['pn1'] ?></a><br>
          <a href="tel: +<?php echo $contact_r['pn2'] ?>" class="d-inline-block mb-2 text-decoration-none text-dark" >
          <i class="bi bi-telephone-outbound-fill"></i>&nbsp;&nbsp;+<?php echo $contact_r['pn2'] ?></a>
      </div>
      <div class="bg-light p-4 mb-4 shadow">
        <h5><b>Mail</b></h5>
        <a href="mailto: <?php echo $contact_r['mail'] ?>" class="d-inline-block mb-2 text-decoration-none text-dark" >
        <i class="bi bi-envelope-at-fill"></i>&nbsp;&nbsp;<?php echo $contact_r['mail'] ?></a><br>
        
      </div>
      <div class="bg-light p-4 mb-4 shadow">
        <h5><b>Follow Us</b></h5>
        <a href="<?php echo $contact_r['insta'] ?>" target="_blank"  class="d-inline-block mb-2 text-decoration-none text-dark" >
        <i class="bi bi-instagram"></i>&nbsp;&nbsp; Instagram</a><br>
          <a href="<?php echo $contact_r['fb'] ?>" target="_blank"  class="d-inline-block mb-2 text-decoration-none text-dark" >
          <i class="bi bi-facebook"></i>&nbsp;&nbsp; Facebook</a>
          
      </div>

    </div>
  </div>
</div>

<!-- contact us end-->


<!-- reset password modal-->


<div class="modal fade shadow-none" id="resetModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="reset-form">
      <div class="modal-header">
        <h5 class="modal-title d-flex align-items-center"><i class="bi bi-key-fill"></i>&nbsp;&nbsp;Create New Password</h5>
      
      </div>
      <div class="modal-body bg-dark text-light">
      <div class="mb-3">
        <label class="form-label">New Password</label>
        <input type="password" name="pass" required class="form-control shadow-none">
        <input type="hidden" name="email">
        <input type="hidden" name="reset_token">
     </div>
     <div class="mb-2 text-end"> 
     
      <button type="button" class="btn bg-light text-dark shadow-none " data-bs-dismiss="modal">
          Cancel
      </button>
      <button type="submit" class="btn btn-dark shadow-none bg-success text-white">Submit</button>
      
     </div>
     </div>
    </form>
    </div>
  </div>
</div>


<!-- reset password modal-->




<?php
require('inc/footer.php');
?>


<?php


  if(isset($_GET['email']) && isset($_GET['reset_token']))
  {
      $data = filteration($_GET);

      date_default_timezone_set('Asia/Kolkata');
      $date = date("Y-m-d");

      $query = select("SELECT * FROM `user_cred` WHERE `email`=? AND `reset_token`=? AND `token_expire`=? LIMIT 1",[$data['email'],$data['reset_token'],$date],'sss');

      
        if(mysqli_num_rows($query)==1){
          echo<<<showModal
              <script>
                var myModal = document.getElementById('resetModal');

                myModal.querySelector("input[name='email']").value = '$data[email]';
                myModal.querySelector("input[name='reset_token']").value = '$data[reset_token]';

                var modal = bootstrap.Modal.getOrCreateInstance(myModal);
                modal.show();
              </script>
            

          showModal;
        }else{
          echo '<script>alert("Invalid Or Expired Link")</script>';
        }
      
  }

?>


<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
 
<!-- slider script -->
  <script>
    var swiper = new Swiper(".mySwiper", {
      spaceBetween: 30,
      centeredSlides: true,
      effect: "fade",
      loop: true,
      autoplay: {
        delay: 3500,
        disableOnInteraction: false,
      },
    
    });
  </script>
<!-- slider script end -->

<!-- testimonial script -->
<script>
    var swiper = new Swiper(".swiper-testimonial", {
      effect: "coverflow",
      grabCursor: true,
      centeredSlides: true,
      slidesPerView: "auto",    
      slidesPerView: "3",
      coverflowEffect: {
        rotate: 50,
        stretch: 0,
        depth: 100,
        modifier: 1,
        slideShadows: true,
      },

      breakpoints: {
        320: {
          slidesPerView: 1,
        },
        640: {
          slidesPerView: 1,
        },
        768: {
          slidesPerView: 2,
        },
        1024: {
          slidesPerView: 3,
        },
      }
    });
</script>
  <!-- testimonial script end -->

  <!-- reset password script-->

  <script>

    let reset_form = document.getElementById('reset-form');
    reset_form.addEventListener('submit', function(e){
    e.preventDefault();

    let data = new FormData();
    data.append('email',reset_form.elements['email'].value);
    data.append('reset_token',reset_form.elements['reset_token'].value);
    data.append('pass',reset_form.elements['pass'].value);
    data.append('reset_pass','');

    var myModal = document.getElementById('resetModal');
    var modal = bootstrap.Modal.getInstance(myModal);
    modal.hide();

    let xhr = new XMLHttpRequest();
    xhr.open("POST","ajax/login_register.php",true);

    xhr.onload = function(){
     if(this.responseText == 'failed')
      {
        alert('Password Reset Failed!');
      }
      else{
       alert('New Password Created');
       reset_form.reset();
      }
      

    }

    xhr.send(data);
 });



  </script>

   <!-- reset password script end-->


</body>
</html>
