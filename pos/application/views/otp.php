<!DOCTYPE html>
<html lang="zxx">
<head>
    <title><?php print $SITE_TITLE; ?> | OTP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <!-- External CSS libraries -->
    <link type="text/css" rel="stylesheet" href="<?php echo $theme_link; ?>assets/css/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="<?php echo $theme_link; ?>assets/fonts/font-awesome/css/font-awesome.min.css">
    <link type="text/css" rel="stylesheet" href="<?php echo $theme_link; ?>assets/fonts/flaticon/font/flaticon.css">

    <!-- Favicon icon -->
    <link rel="shortcut icon" href="<?php echo $theme_link; ?>images/favicon.ico" type="image/x-icon" >

    <!-- Google fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet">

    <!-- Custom Stylesheet -->
    <link type="text/css" rel="stylesheet" href="<?php echo $theme_link; ?>assets/css/style.css">

</head>












<!-- Login 4 start -->
<div class="login-4">
    <div class="container-fluid">
        <div class="row login-box">
            <div class="col-lg-6 form-section">
                <div class="form-inner">
                    <a href="/" class="logo">
                        <img src="<?php echo base_url(get_site_logo());?>" alt="logo">
                    </a>
                    <h3>এখানে আপনার ইমেইল থেকে পাওয়া ওটিপি লিখুন</h3>

     <div class="text-danger tex-center"><?php echo $this->session->flashdata('failed'); ?></div>
     <div class="text-success tex-center"><?php echo $this->session->flashdata('success'); ?></div>


                    <form action="<?php echo $base_url; ?>login/verify_otp" method="POST" id="password-form">


      <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
      <input type="hidden" name="email" id="email" value="<?= $this->session->userdata('email')?>">

                        <div class="form-group position-relative clearfix">
                            <input name="otp" type="password" class="form-control" placeholder="এখানে ওটিপি লিখুন" id="otp">
                        </div>
                        <div class="form-group mb-0 clearfix">
                            <button type="submit" class="btn btn-lg btn-primary btn-theme">সাবমিট করুন</button>
                        </div>
                        
						 <div class="extra-login clearfix">
                            <span>আমাদেরকে অনুসরণ করুন</span>
                        </div>
                        <div class="clearfix"></div>
                        <ul class="social-list">
                            <li><a href="https://facebook.com/official.elitedesign" class="facebook-color"><i class="fa fa-facebook facebook-i"></i><span>ফেইসবুক</span></a></li>
                            <li><a href="https://www.youtube.com/@elitedesignbd" class="twitter-color"><i class="fa fa-youtube-play google-i"></i><span>ইউটিউব</span></a></li>
                            <li><a href="https://www.elitedesign.com.bd" class="google-color"><i class="fa fa-globe twitter-i"></i><span>ওয়েব সাইট</span></a></li>
                        </ul>
                    </form>
                    <div class="clearfix"></div>
                    <p>সফটওয়্যারের সকল কারিগরী সহযোগিতায় <a href="https://www.elitedesign.com.bd" class="thembo">এলিট ডিজাইন</a></p>
                </div>
            </div>
            <div class="col-lg-6 bg-img clip-home h-100"></div>
        </div>
    </div>
</div>
<!-- Login 4 end -->
















<!-- External JS libraries -->
<script src="<?php echo $theme_link; ?>assets/js/jquery-3.6.0.min.js"></script>
<script src="<?php echo $theme_link; ?>assets/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo $theme_link; ?>assets/js/jquery.validate.min.js"></script>
<script src="<?php echo $theme_link; ?>assets/js/app.js"></script>
<!-- Custom JS Script -->

<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });
</script>
<script type="text/javascript" >
$(function($) { // this script needs to be loaded on every page where an ajax POST may happen
    $.ajaxSetup({ data: {'<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>' }  }); });
</script>

</body>
</html>
