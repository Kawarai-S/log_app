<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>ログイン</title>
     <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="Form">
        <form method="POST" action="login_act.php">
            <div class="Form-Item">
                <p class="Form-Item-Label"> ID</p>
                <input class="Form-Item-Input" type="text" name="lid">
            </div>
            <div class="Form-Item">
                <p class="Form-Item-Label"> パスワード</p>
                <div class="password_wrapper">
                    <input class="password_input"  type="text" name="lpw">
                    <button class="password_toggle" type="button"></button>
                </div>
            </div>
            <input class="Form-Btn" type="submit" value="OK">
        </form>
    </div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script>
    $('.password_toggle').click(function(e){
        const input = $(this).prev();
        const type =input.attr('type');
        input.attr('type',type === 'text'?'password':'text');
        $(this).toggleClass('is-visible');
    });
</script>
</body>
</html>