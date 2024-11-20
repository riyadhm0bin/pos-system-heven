<!DOCTYPE html>
<html lang="zxx">
<head>
    <title><?php print $SITE_TITLE; ?> | Forgot Password</title>
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
<body id="top">
<div class="page_loader"></div>

<!-- Login 4 start -->
<div class="login-4">
    <div class="container-fluid">
        <div class="row login-box">
<!--            <div class="col-lg-6 form-section">-->
            <div class="col-lg-12 form-section">
                <div class="form-inner">
                    <a href="/" class="logo">
                        <img src="<?php echo base_url(get_site_logo());?>" alt="logo">
                    </a>
                    <h3>পাসওয়ার্ড রিকভার করুন</h3>


                    <div class="text-danger tex-center"><?php echo $this->session->flashdata('failed'); ?></div>

                    <form action="<?php echo $base_url; ?>login/send_otp" method="POST" id="commonForm">
                        <div class="form-group position-relative clearfix">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                            <input name="email" type="text" class="form-control" placeholder="এখানে ইমেইল এড্রেস লিখুন" id="email" name="email" aria-label="Email Address">
                        </div>
                        <div class="form-group mb-0 clearfix">
                            <button type="submit" class="btn btn-lg btn-primary btn-theme">ওটিপি সেন্ড করুন</button>
                        </div>

                        <div class="extra-login clearfix">
                            <span>আমাদেরকে অনুসরণ করুন</span>
                        </div>
                        <div class="clearfix"></div>
                        <ul class="social-list">
                            <li><a href="https://facebook.com/heaventechit2.0" target="_blank" class="facebook-color"><i class="fa fa-facebook facebook-i"></i><span>ফেইসবুক</span></a></li>
                            <li><a href="https://www.youtube.com/heaventechit2.0" target="_blank" class="twitter-color"><i class="fa fa-youtube-play google-i"></i><span>ইউটিউব</span></a></li>
                            <li><a href="https://heaventechit.com" target="_blank" class="google-color"><i class="fa fa-globe twitter-i"></i><span>ওয়েব সাইট</span></a></li>
                        </ul>
                    </form>
                    <div class="clearfix"></div>
                    <p>সফটওয়্যারের সকল কারিগরী সহযোগিতায় <a style="text-decoration: underline;" href="https://heaventechit.com" class="thembo">Heaven Tech-IT</a></p>
                </div>
            </div>
<!--            <div class="col-lg-6 bg-img clip-home h-100"></div>-->
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
