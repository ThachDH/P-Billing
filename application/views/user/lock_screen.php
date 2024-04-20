<?php
defined('BASEPATH') or exit('');
?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="referrer" content="origin-when-crossorigin" id="meta_referrer" />
   <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
   <meta http-equiv="Pragma" content="no-cache" />
   <meta http-equiv="Expires" content="0" />
   <title><?= $title; ?></title>
   <!--    favicon-->
   <link rel="icon" href="<?= base_url('assets/img/icons/favicon.ico'); ?>" type="image/ico">
   <!-- GLOBAL MAINLY STYLES-->
   <link href="<?= base_url('assets/vendors/jquery-ui/jquery-ui.css'); ?>" rel="stylesheet" />
   <link href="<?= base_url('assets/vendors/bootstrap/dist/css/bootstrap.min.css'); ?>" rel="stylesheet" />
   <link href="<?= base_url('assets/vendors/font-awesome/css/font-awesome.min.css'); ?>" rel="stylesheet" />
   <link href="<?= base_url('assets/vendors/line-awesome/css/line-awesome.min.css'); ?>" rel="stylesheet" />
   <link href="<?= base_url('assets/vendors/themify-icons/css/themify-icons.css'); ?>" rel="stylesheet" />

   <!--    CUSTOMIZE FOR DATATABLES-->
   <link href="<?= base_url('assets/css/custom.datatables.css'); ?>" rel="stylesheet" />

   <!-- THEME STYLES-->
   <link href="<?= base_url('assets/css/main.min.css'); ?>" rel="stylesheet" />
   <link href="<?= base_url('assets/css/ebilling.css'); ?>" rel="stylesheet" />
   <!-- PAGE LEVEL STYLES-->

   <!-- CORE PLUGINS-->
   <script src="<?= base_url('assets/vendors/popper.js/dist/umd/popper.min.js'); ?>"></script>

   <script src="<?= base_url('assets/vendors/jquery/dist/jquery.min.js'); ?>"></script>
   <script src="<?= base_url('assets/vendors/jquery/dist/jquery2-1-4.min.js'); ?>"></script>
   <script src="<?= base_url('assets/vendors/jquery-ui/jquery-ui.js'); ?>"></script>
   <script src="<?= base_url('assets/vendors/jquery-validation/dist/jquery.validate.min.js'); ?>"></script>

   <style>
      body {
         background: url('<?= base_url('assets/img/123.jpg') ?>') no-repeat center center fixed;
         -webkit-background-size: cover;
         -moz-background-size: cover;
         -o-background-size: cover;
         background-size: cover;
      }

      .cover {
         position: absolute;
         top: 0;
         bottom: 0;
         left: 0;
         right: 0;
         background-color: rgba(117, 54, 230, .1);
      }

      .login-content {
         max-width: 450px;
         margin: 100px auto 50px;
      }

      .auth-head-icon {
         position: relative;
         height: 60px;
         width: 60px;
         display: inline-flex;
         align-items: center;
         justify-content: center;
         font-size: 26px;
         background-color: #fff;
         color: #5c6bc0;
         box-shadow: 0 5px 20px #d6dee4;
         border-radius: 50%;
         transform: translateY(-50%);
         z-index: 2;
      }
   </style>
</head>

<body>
   <div class="cover"></div>
   <div class="ibox login-content">
      <div class="text-center">
         <span class="auth-head-icon"><i class="ti-lock"></i></span>
      </div>
      <form class="ibox-body pt-0" id="lock-form" action="javascript:;" method="POST">
         <h4 class="font-strong text-center mb-4"><?= mb_strtoupper($this->session->userdata('UserName')); ?></h4>
         <div class="row pt-3">
            <div class="col-4">
               <img class="img-circle" src="<?= base_url('assets/img/users/user-default.jpg') ?>" alt="image" width="110" />
            </div>
            <div class="col-8">
               <p class="font-13">Nhập mật khẩu của bạn để mở khoá màn hình</p>
               <p class="font-13 text-danger font-italic"><span id="error-content"></span></p>
               <div class="form-group">
                  <input class="form-control" type="password" id="password_" name="password" placeholder="******">
               </div>
               <div class="form-group">
                  <button class="btn btn-primary btn-block" onclick="unlockScreen()" type="submit">
                     <span class="btn-icon"><i class="ti-lock"></i>MỞ KHOÁ</span>
                  </button>
               </div>
            </div>
         </div>
      </form>
   </div>
   <!-- PAGE LEVEL SCRIPTS-->
   <script>
      function unlockScreen() {
         var ifrm = parent.document.getElementById('ifrmLockScr');
         var pwd = ifrm.contentWindow.document.getElementById('password_').value;
         if (localStorage.getItem('lock_screen_pwd') == pwd) {
            ifrm.contentWindow.document.getElementById('error-content').innerText = '';
            localStorage.removeItem('lock_screen');
            localStorage.removeItem('lock_screen_pwd');
            ifrm.remove();
         }
         else {
            ifrm.contentWindow.document.getElementById('error-content').innerText = 'Sai mật khẩu!';
         }
         // document.getElementById('password_').value;

         // ifrm.contentWindow.document.getElementById('password_').value
      }
   </script>
</body>



</html>