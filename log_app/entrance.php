<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PET LOG</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    
    <link rel="stylesheet" href="css/entrance.css">
</head>
<body>
<div class="wrap">
    <!-- <div>
        <h1>PET HEALTH</h1>
    </div> -->
    <div class="login">
        <div class="toggle-bar">
            <div class="toggle-login active">
                <span>Login</span>
            </div>
            <div class="toggle-register">
                <span>Sign up</span>
            </div>
        </div>
        <form method="POST" action="login_act.php">  
            <div class="login-body">
            <form method="POST" action="login_act.php">  
                <div class="input-section">
                <i class="fa-solid fa-id-card-clip"></i>
                    <input class="user-input" type="text" name="lid" placeholder="UserID">
                </div>
                <div class="input-section">
                    <i class="fas fa-lock"></i>
                    <input class="user-input" type="password" name="lpw" placeholder="Password">
                </div>
                <p id="forgot-password">Forgot your password?</p>
                <button class="btn" id="btn-login" type="submit">Login</button>
            </div>
        </form>
        <form method="POST" action="sign_up_insert.php">    
            <div class="register-body" style="display:none;">
            
                <div class="input-section">
                    <i class="fas fa-user"></i>
                    <input class="user-input" type="text" name="name" placeholder="UserName">
                </div>
                <div class="input-section">
                    <i class="fa-solid fa-id-card-clip"></i>
                    <input class="user-input" type="text" name="lid" placeholder="UserID">
                </div>
                <div class="input-section">
                    <i class="fas fa-lock"></i>
                    <input class="user-input" type="password" name="lpw" placeholder="Password" maxlength="64">
                </div>
                <p id="registered">Signed up already?</p>
                <button class="btn btn-action" id="btn-register" type="submit">Sign up</button>
            </div> 
        </form>
         
    </div>
    
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script>
    $('.toggle-register').click(function(){
        $(this).addClass('active');
        $('.toggle-login').removeClass('active');
        $('.login-body').slideUp("slow");
        $('.register-body').delay(625).slideDown("slow");
    });

    $('.toggle-login').click(function(){
        $(this).addClass('active');
        $('.toggle-register').removeClass('active');
        $('.register-body').slideUp("slow");
        $('.login-body').delay(625).slideDown("slow");
    });

    $('#registered').click(function(){
        $('.toggle-login').click();
    });

</script>
</body>
</html>