
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">

        <link rel="shortcut icon" type="image/x-icon" href="img/plismun19_a_favicon.png">

        <title>Manage Delegation – PLISMUN 2021</title>

        <!-- Bootstrap Core CSS -->

        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">

        <!-- Fonts -->
        <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link href="css/animate.css" rel="stylesheet" />

        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css" rel="stylesheet">

        <!-- bootstrap select -->
        <link href="css/bootstrap-select.min.css" rel="stylesheet">

        <link href="css/checkboxes.css" rel="stylesheet">

        <!-- recycling css from signup page because the layout is basically the same and I can't be bothered to make a new file just for this-->
        <link href="css/pages/managedelegation.css" rel="stylesheet">
        <link href="css/index.css" rel="stylesheet">
        <link href="color/default.css" rel="stylesheet">

        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-120398250-1"></script>
        <script>
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());

              gtag('config', 'UA-120398250-1');
        </script>

    </head>

    <body id="page-top" data-spy="scroll" data-target=".navbar-custom">
        <?php
        session_start();
        require_once('class.phpmailer.php');
        require_once('config.php');

        if (isset($_POST['submit'])) {
            $userid_toremove = $_POST['userid'];

            $email = $_SESSION['id'];
            $userid = mysqli_fetch_assoc(mysqli_query($link, "SELECT id FROM users WHERE email = '$email'"))['id'];

            $remove_query = mysqli_query($link, "UPDATE delegates SET delegation = 'none' WHERE userid = $userid_toremove");
            $remove_query2 = mysqli_query($link, "UPDATE delegations SET delegates = delegates - 1 WHERE userid = $userid");

            $delegate_toremove_info = mysqli_fetch_assoc(mysqli_query($link, "SELECT email, firstname, lastname FROM users WHERE id = $userid_toremove"));

            // phpmailer EMAIL

            $mail = new PHPMailer();
            $body =
                "<h2>You have been removed from your delegation by your delegation leader</h2>
                <p>Dear ".$delegate_toremove_info['firstname']. ' ' .$delegate_toremove_info['lastname'].", </p>
                <p>This email is to notify you that you have been removed from your delegation at PLISMUN by your delegation leader. </p>
                <p>Kindly contact your delegation leader if you believe this is an error.</p>
                <p><br><br><i>This is an automated message generated by <a href='plismun.com'>plismun.com</a>. Please do not reply to this email. If you would like to contact us, head to <a href='plismun.com/contact'>plismun.com/contact</a> </i></p>";

            $mail->IsSMTP(); // telling the class to use SMTP

            $mail->SMTPAuth   = true;                  // enable SMTP authentication
            $mail->Host       = "mx1.hostinger.com"; // sets the SMTP server
            $mail->Port = 587;

            $mail->Username   = "info@plismun.com"; // SMTP account username
            $mail->Password   = "plismun123";        // SMTP account password

            $mail->SetFrom('info@plismun.com', 'PLISMUN Notification');

            // $mail->AddReplyTo("name@yourdomain.com","First Last");

            $mail->Subject = "You have been removed from your PLISMUN19 delegation";


            $mail->MsgHTML($body);

            $address = $delegate_toremove_info['email'];
            $mail->AddAddress($address);


            if(!$mail->Send()) {
                $removeresult = '<div class="alert alert-danger">An error occurred. Please try again</div>';
            } else {
                $removeresult = '<div class="alert alert-success">Delegate removed successfully</div>';
            }

        }

        ?>


        <!-- Preloader -->
        <div id="preloader-overlay"></div>


        <!-- navbar, inserted via js -->
        <div id="header"></div>




        <?php
        if (isset($_SESSION['id']) && $_SESSION['position'] == 'delegationleader')
        {
            $email = $_SESSION['id'];
            $userid = mysqli_fetch_assoc(mysqli_query($link, "SELECT id FROM users WHERE email = '$email'"))['id'];


            $delegation = mysqli_fetch_assoc(mysqli_query($link, "SELECT name FROM delegations WHERE userid = $userid"))['name'];
            $delegationleader = mysqli_fetch_assoc(mysqli_query($link, "SELECT firstname FROM users WHERE id = $userid"))['firstname'] . ' ' . mysqli_fetch_assoc(mysqli_query($link, "SELECT lastname FROM users WHERE id = $userid"))['lastname'];

            $delegate_num = mysqli_fetch_assoc(mysqli_query($link, "SELECT delegates FROM delegations WHERE userid = $userid"))['delegates'];

            ?>
            <section id="intro" class="intro parallax-window" data-parallax="scroll" data-image-src="img/school_img2.jpg">
            </section>

            <div class="container" style="margin-top:30px;">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4">
                        <?php echo $removeresult ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <p style="font-size: 20px;">Delegation of</p>
                        <h2 style="font-size: 50px;"><?php echo $delegation; ?></h2>

                        <hr class="bottom-line">
                    </div>

                    <div class="col-md-3 col-md-offset-3 text-center">
                        <p style="font-size:30px;">Delegation Leader <br /><b><?php echo $delegationleader; ?></b></p>
                    </div>
                    <div class="col-md-3 text-center">
                        <p style="font-size:30px;">Delegates <br /><b><?php echo $delegate_num; ?></b></p>
                    </div>
                </div>

                <hr class="bottom-line">

                <div class="text-center row">
                    <p style="font-size:30px;">Delegates</p>

                    <table class="table col-md-12">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Committee</th>
                                <th scope="col">Country</th>
                                <th scope="col">Application Status</th>
                                <th scope="col">Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            $result = mysqli_query($link, "SELECT delegates FROM delegations WHERE userid = $userid");
                            if (mysqli_fetch_assoc($result)['delegates'] > 0) {

                                for ($x = 1; $x <= mysqli_fetch_assoc(mysqli_query($link, "SELECT delegates FROM delegations WHERE userid = $userid"))['delegates']; $x++)
                                {
                                    $y = $x - 1;

                                    $delegate_info = mysqli_fetch_assoc(mysqli_query($link, "SELECT userid, committee, country FROM delegates WHERE delegation = '$delegation' LIMIT 1 OFFSET $y"));
                                    $delegate_userid = $delegate_info['userid'];
                                    $delegate_committee = $delegate_info['committee'];
                                    $delegate_country = $delegate_info['country'];

                                    $delegate_userinfo = mysqli_fetch_assoc(mysqli_query($link, "SELECT email, firstname, lastname FROM users WHERE id = $delegate_userid"));
                                    $delegate_email = $delegate_userinfo['email'];
                                    $delegate_name = $delegate_userinfo['firstname']. ' ' .$delegate_userinfo['lastname'];

                                    ?>
                                    <tr style="text-align: left;">
                                        <th scope="row"><?php echo $delegate_name; ?></th>
                                        <td><?php echo $delegate_email; ?></td>
                                        <td><?php echo $delegate_committee; ?></td>
                                        <td><?php echo $delegate_country; ?></td>
                                        <?php
                                        if ($delegate_committee == '' && $delegate_country == '') {
                                            ?>
                                            <td class="text-yellow"><i class="fas fa-clock"></i> Pending</td>
                                            <?php
                                        } elseif ($delegate_committee == 'REJECTED' && $delegate_country == 'REJECTED') {
                                            ?>
                                            <td class="text-red"><i class="fas fa-times"></i> Rejected</td>
                                            <?php
                                        } else {
                                            ?>
                                            <td class="text-green"><i class="fas fa-check"></i> Accepted</td>
                                            <?php
                                        }
                                        ?>
                                        <td>
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#<?php echo 'confirmremoval'.$delegate_userid; ?>">
                                                Remove from delegation
                                            </button>

                                            <div class="modal fade" id="<?php echo 'confirmremoval'.$delegate_userid; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Remove delegate from delegation?</h5>
                                                            <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top:0;">
                                                                <span aria-hidden="true"><i class="fas fa-times"></i></span>
                                                            </button> -->


                                                        </div>

                                                        <div class="modal-body">
                                                            <p>Are you sure you want to remove <b><?php echo $delegate_name; ?></b>? This action cannot be undone and the delegate will not be able to rejoin the delegation!</p>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal" style="display: inline;">Close</button>
                                                            <form method="post" action="managedelegation" style="display: inline;">
                                                                <input type="hidden" value="<?php echo $delegate_userid; ?>" name="userid">

                                                                <input id="<?php echo 'remove'.$delegate_userid; ?>" name="submit" type="submit" class="btn btn-primary btn-send" value="Remove">
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php
                                }
                            }

                            ?>
                        </tbody>

                    </table>
                </div>
            </div>

        <?php } else { ?>
            <h1 style="text-align: center; margin-top: 10%;">You do not have the valid credentials to view this page. </h1>
        <?php } ?>





        <!-- Core JavaScript Files -->
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/jquery.scrollTo.js"></script>
        <!--reveal while scrolling script-->
        <script src="js/wow.min.js"></script>
        <!-- Custom Theme JavaScript -->
        <script src="js/custom.js"></script>
        <!-- parallax script -->
        <script src="js/parallax.min.js"></script>
        <!-- bootstrap select js -->
        <script src="js/bootstrap-select.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js"></script>


        <script type="text/javascript">
            // include footer
            $(function() {
                $("#header").load("navbar");
                $("#footer").load("footer");
                $("#preloader-overlay").load("preloader");
            });

            $('.datepicker').datepicker();

            $(function () {
              $('[data-toggle="tooltip"]').tooltip()
            })
        </script>

    </body>
</html>
