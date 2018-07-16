<?
require_once('main.php');

$foodId = $_POST['foodId'];
$session = $_COOKIE["session"];
$studentNumber = $_COOKIE["studentNumber"];
if(authenticate($studentNumber,$session)){
    $db = Db::getInstance();
    $record = $db->first("SELECT DayOfWeek,Name FROM Foods WHERE ID='$foodId'");
    $DayOfWeek = $record['DayOfWeek'];
    echo "<div style='text-align: center'>";
    if ($record == null){
        $message = "غذایی با این شماره وجود ندارد";
        require_once("failed.php");
        br();
        br();
        br();
        echo "<a href='index.php'>بازگشت به صفحه انتخاب غذا</a>";
        exit;
    } else if(!Day($DayOfWeek)) {
        $message = "شما در بازه‌ی زمانی نیستید که بتوانید این غذا را رزرو کنید";
        require_once("failed.php");
        br();
        br();
        br();
        echo "<a href='index.php'>بازگشت به صفحه انتخاب غذا</a>";
        exit;
    }else{
        $DayOfWeek = $record['DayOfWeek'];
        $Name = $record['Name'];
        $reserved = $db->insert("INSERT INTO food_reservation (foodId,studentNumber,Name,DayOfWeek) values ('$foodId','$studentNumber','$Name','$DayOfWeek')");
        $message = "غذای شما با موفقیت ثبت شد";
        require_once("succeed.php");
        br();
        br();
        br();
        echo "<a href='index.php'>بازگشت به صفحه انتخاب غذا</a>";
        exit;
    }
    echo "</div>";
}


?>