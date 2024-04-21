<?php
$conn = mysqli_connect("localhost", "root", "", "realestatephp");

session_start();
if (isset($_POST['btn'])) {

    if (!isset($_SESSION['otp'])) {
        header("Location: login.php");
        exit;
    }

    $expected_otp = $_SESSION['otp'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $entered_otp = "";
        $otp = $_POST['otp'];
        foreach ($otp as $digit) {
            $entered_otp .= $digit;
        }

        if ($entered_otp == $expected_otp) {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Include PHPMailer library
                require 'smtp/PHPMailerAutoload.php';
            
                $email=$_SESSION['user_email'] ;     
                function smtp_mailer($to, $subject, $msg) {
                    $mail = new PHPMailer();
                    $mail->IsSMTP();
                    $mail->SMTPAuth = true;
                    $mail->SMTPSecure = 'tls';
                    $mail->Host = "smtp.gmail.com";
                    $mail->Port = 587;
                    $mail->IsHTML(true);
                    $mail->CharSet = 'UTF-8';
                    //$mail->SMTPDebug = 2; 
                    $mail->Username = "apnaghar024@gmail.com"; // Replace with your email
                    $mail->Password = "iidt oyle eoqv msvd";        
                    $mail->SetFrom("apnaghar024@gmail.com"); 
                    $mail->Subject = $subject;
                    $mail->Body = $msg;
                    $mail->AddAddress($to);
                    $mail->SMTPOptions = array('ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => false
                    ));
                    if (!$mail->Send()) {
                        return $mail->ErrorInfo;
                    } else {
                        return 'Sent';
                    }
                }
            
                $subject = "Sign Up Successful";
                $msg = "Thank you for signing up!";
            
            
                $result = smtp_mailer($email, $subject, $msg);
        
                if ($result === 'Sent') {
                    $sql1 = "SELECT * FROM user WHERE `uemail`= '$email'";
                    $res = mysqli_query($conn, $sql1);
                    $row1 = mysqli_fetch_assoc($res);
                    $_SESSION['user_id'] = $row1['user_id'];
                    header("Location: index.php");
                    exit;
                } else {
                    echo "<script>alert('Failed to send email.');</script>";
                }
            }
            exit;
        } else {
            echo "<script>alert('Invalid OTP. Please try again.')</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Login</title>
    <style>
        /* Custom CSS */
        .otp-field {
            flex-direction: row;
            column-gap: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .otp-field input {
            height: 45px;
            width: 42px;
            border-radius: 6px;
            outline: none;
            font-size: 1.125rem;
            text-align: center;
            border: 1px solid #ddd;
        }

        .otp-field input:focus {
            box-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
        }

        .otp-field input::-webkit-inner-spin-button,
        .otp-field input::-webkit-outer-spin-button {
            display: none;
        }

        .resend {
            font-size: 12px;
        }
    </style>
</head>

<body class="container-fluid bg-body-tertiary d-block">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6 col-lg-4" style="min-width: 500px;">
            <div class="card bg-white mb-5 mt-5 border-0" style="box-shadow: 0 12px 15px rgba(0, 0, 0, 0.02);">
                <div class="card-body p-5 text-center" align="center">
                    <h4>Verify</h4>
                    <p>Your code was sent to you via email</p>

                    <form method="post"> <!-- Form starts here -->
                        <div class="otp-field mb-4">
                            <input type="number" maxlength="1" name="otp[]" />
                            <input type="number" maxlength="1" name="otp[]" disabled />
                            <input type="number" maxlength="1" name="otp[]"disabled />
                            <input type="number" maxlength="1" name="otp[]" disabled/>
                            <input type="number" maxlength="1" name="otp[]" disabled/>
                            <input type="number" maxlength="1" name="otp[]" disabled/>
                        </div>
                        <br>
                        <input type="submit" name="btn" value="Verify" class="btn btn-primary">
                    </form> <!-- Form ends here -->

                    <p class="resend text-muted mb-0">
                        Didn't receive code? <a href="sign_up_and_login.php">sign up again</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <script src="otp.js"></script>


</body>

<script>
    const inputs = document.querySelectorAll(".otp-field > input");
    const button = document.querySelector(".btn");

    window.addEventListener("load", () => inputs[0].focus());

    inputs[0].addEventListener("paste", function(event) {
        event.preventDefault();

        const pastedValue = (event.clipboardData || window.clipboardData).getData(
            "text"
        );
        const otpLength = inputs.length;

        for (let i = 0; i < otpLength; i++) {
            if (i < pastedValue.length) {
                inputs[i].value = pastedValue[i];
                inputs[i].removeAttribute("disabled");
                inputs[i].focus();
            } else {
                inputs[i].value = ""; // Clear any remaining inputs
                inputs[i].focus();
            }
        }
    });

    inputs.forEach((input, index1) => {
        input.addEventListener("keyup", (e) => {
            const currentInput = input;
            const nextInput = input.nextElementSibling;
            const prevInput = input.previousElementSibling;

            if (currentInput.value.length > 1) {
                currentInput.value = "";
                return;
            }

            if (
                nextInput &&
                nextInput.hasAttribute("disabled") &&
                currentInput.value !== ""
            ) {
                nextInput.removeAttribute("disabled");
                nextInput.focus();
            }

            if (e.key === "Backspace") {
                inputs.forEach((input, index2) => {
                    if (index1 <= index2 && prevInput) {
                        input.setAttribute("disabled", true);
                        input.value = "";
                        prevInput.focus();
                    }
                });
            }

            button.classList.remove("active");
            button.setAttribute("disabled", "disabled");

            const inputsNo = inputs.length;
            if (!inputs[inputsNo - 1].disabled && inputs[inputsNo - 1].value !== "") {
                button.classList.add("active");
                button.removeAttribute("disabled");

                return;
            }
        });
    });
</script>

</html>