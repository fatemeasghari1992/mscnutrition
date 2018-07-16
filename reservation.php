<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</head>
<body>
<div class='container'>
    <div class='row'>
        <div class='col-md-8 col-md-offset-3'>
<?
require_once('main.php');

$foodId = $_POST['foodId'];
$session = $_COOKIE["session"];
$studentNumber = $_COOKIE["studentNumber"];
if(authenticate($studentNumber,$session)){
    $db = Db::getInstance();
    $record = $db->first("SELECT DayOfWeek,Name FROM Foods WHERE ID='$foodId'");
    $isReserved = $db->first("SELECT id FROM food_reservation WHERE foodId='$foodId'");
    if ($record == null){
        $message = "غذایی با این شماره وجود ندارد";
        require_once("failed.php");

        echo "<a href='index.php' class='btn btn-warning'>بازگشت به صفحه انتخاب غذا</a>";
        //exit;
    } else if(isset($isReserved)) {
        $message = "این وعده قبلا رزرو شده است";
        require_once("succeed.php");
        echo "<a href='index.php' class='btn btn-warning'>بازگشت به صفحه انتخاب غذا</a>";
        exit;
    }else{
        $DayOfWeek = $record['DayOfWeek'];
        $Name = $record['Name'];
        $reserved = $db->insert("INSERT INTO food_reservation (foodId,studentNumber,Name,DayOfWeek) values ('$foodId','$studentNumber','$Name','$DayOfWeek')");
        $message = "غذای شما با موفقیت ثبت شد";
        require_once("succeed.php");

        echo "<a href='index.php' class='btn btn-warning'>بازگشت به صفحه انتخاب غذا</a>";
      //  exit;
    }
}else{
    $message = "شماره دانشجویی شما ثبت نشده است.لطفا به صفحه ورود مراجعه نمایید";
    require_once("failed.php");

    echo "<a href='http://127.0.0.1:8083/index.php' class='btn btn-danger col-md-offset-2'>بازگشت به صفحه ورود</a>";

    //exit;
}


?>
</div>
</div>
</div>
</body>
</html>
