<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>対象を登録</title>
    <link rel="stylesheet" href="css/reset.css">
     <link rel="stylesheet" href="css/forms.css">
</head>
<body>
<div class="wrap">
    <div class="Form">
        <p class="title">ペットの登録</p>
        <form method="POST" action="register_insert.php" enctype="multipart/form-data">
            <div class="Form-Item">
                <p class="Form-Item-Label">名前</p>
                <input  class="Form-Item-Input" type="text" name="name">
            </div>
            <div class="Form-Item">
                <p class="Form-Item-Label">性別</p>
                    <select  class="Form-Item-Input" name="gender">
                        <option value="">選択してください</option>
                        <option value="male">♂</option>
                        <option value="female">♀</option>
                    </select>
            </div>
            <div class="Form-Item">
                <p class="Form-Item-Label">誕生日</p>
                <input  class="Form-Item-Input"  type="date" name="birth">
                </p>
            </div>
            <div class="Form-Item">
                <p class="Form-Item-Label"> 種類</p>
                <input class="Form-Item-Input" type="text" name="type">
            </div>
            <div class="Form-Item">
                <p class="Form-Item-Label">写真</p>
                <input type="file" name="photo" accept="image/*">
                <div class="cms-thumb">
                    <img src="" width="200px">
                </div>
            </div>
            <input class="Form-Btn" type="submit" value="OK">
        </form>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script>
  $(document).ready(function() {
    $('input[type=file]').change(function(){
      var file = $(this).prop('files')[0];
      if (!file.type.match('image.*')) {
        $(this).val('');
        $('.cms-thumb > img').attr('src', '');
        return;
      }
      var reader = new FileReader();
      reader.onload = function() {
        $('.cms-thumb > img').attr('src', reader.result);
      }
      reader.readAsDataURL(file);
    });
  });
</script>
</body>
</html>