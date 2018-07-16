<?php
require_once('main.php');
$foodId = $_POST['foodId'];
$session = $_COOKIE["session"];
$studentNumber = $_COOKIE["studentNumber"];
$db = Db::getInstance();
if(authenticate($studentNumber,$session)) {

    $record = $db->first("SELECT ID FROM food_reservation WHERE foodId='$foodId'");

    echo "<div style='text-align: center'>";
    if ($record == null){
        $message = "غذایی با این شماره برای شما رزرو نشده است";
        require_once("failed.php");
        br();
        br();
        br();
        echo "<a href='reserved.php' class='btn btn-danger'>بازگشت </a>";
        exit;
    }else{
        $record = $db->query("DELETE FROM food_reservation WHERE foodId='$foodId'");
        $message = "غذای شما با موفقیت حذف شد";
        require_once("succeed.php");
        br();
        br();
        br();
        echo "<a href='reserved.php' class='btn btn-warning'>بازگشت </a>";
    }
}else{
    $message = "شماره دانشجویی شما ثبت نشده است.لطفا به صفحه ورود مراجعه نمایید";
    require_once("failed.php");
    br();
    br();
    br();
    echo "<a href='http://127.0.0.1:8083/index.php' class='btn btn-info'>بازگشت به صفحه ورود</a>";
    exit;
}