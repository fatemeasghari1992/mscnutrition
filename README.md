![dependencies none](https://img.shields.io/badge/Dependencies-none-brightgreen.svg)
![PHP 5.6+](https://img.shields.io/badge/PHP-5.6+-green.svg)
![bootstrap 3.3.5](https://img.shields.io/badge/bootstrap-3.3.5-green.svg)
![JQuery 1.11.3](https://img.shields.io/badge/JQuery-1.11.3-green.svg)
# سیستم تغذیه

###### ساختار کلی پروژه
  - config.php : تنظیمات مورد نیاز برای اتصال به پایگاه داده
  - common.php : توابع مورد نیاز برنامه که به صورت ماژولار نوشته شده اند
  - db.php : کلاس دیتابیس به منظور مدیریت اتصال ها و اجرای کوئری ها
  - deletion.php : حذف واحدهای درسی اخذ شده 
  - exit.php : خروج از سیستم آموزش
  - failed.php , success.php : نمایش پیام های مربوط به عملیات موفقیت آمیز یا خطاهای احتمالی
  - index.php : صفحه اصلی سامانه
  - reservation.php : نمایش لیست غذاها به منظور رزو
  - reserved.php : نمایش لیست غذاهای رزو شده و قابلیت حذف انها
##رمزگذاری کلمه ی عبور

<div dir="rtl">
رمز گذاری کلمه ی عبور (هش کردن) توسط الگوریتم md5 انجام می پذیرد و به منظور افزایش امنیت از یک متن دلخواه به عنوان Salt (نمک) استفاده میکنیم. تابع رمزگذاری درون فایل common.php به صورت زیر پیاده سازی شده است
</div>

``` php
function encryptPassword($password){
  global $config;
  return md5($password . $config['salt']);
}
```
## شرح کلاس db.php
###### در این کلاس دو پراپرتی به شرح زیر داریم:
  - $connection : به منظور نگهداری ارتباط اخذ شده با دیتابیس استفاده میشود
  - $db : به منظور نگهداری اشیائی که از روی کلاس نمونه سازی شده اند استفاده میشود
###### شرح عملکرد متد های کلاس :
``` php
  public function __construct($option = null){
    if ($option != null){
      $host = $option['host'];
      $user = $option['user'];
      $pass = $option['pass'];
      $name = $option['name'];
    } else {
      global $config;
      $host = $config['db']['host'];
      $user = $config['db']['user'];
      $pass = $config['db']['pass'];
      $name = $config['db']['name'];
    }
```
<div dir="rtl">
سازنده ی کلاس در هنگام ایجاد شی از کلاس تنظیمات مربوط به اتصال دیتابیس را انجام میدهد.
درصورتی که تنظیمات در هنگام ایجاد شی به سازنده داده شود از تنظیمات داده شده استفاده میکند و در غیر اینصورت از تنظیمات درون فایل کانفیگ که در بالاتر معرفی شد , استفاده میشود
بعد از اتصال به دیتابیس ,نوع استاندارد کاراکترها بر روی utf8 قرار میگیرد
</div>

```  php
  public static function getInstance($option = null){
    if (self::$db == null){
      self::$db = new Db($option);
    }

    return self::$db;
  }
```
<div dir="rtl">
متد بالا یک نمونه از روی کلاس ایجاد کرده و آن را برمیگرداند.
</div>

``` php
  public function query($sql){
    $result = $this->connection->query($sql);
    $records = array();

    if ($result->num_rows == 0) {
      return null;
    }

    while($row = $result->fetch_assoc()) {
      $records[] = $row;
    }

    return $records;
  }
```
<div dir="rtl">

متد بالا وظیفه ی اجرای دستورات sql و برگرداندن نتیجه ی حاصل از اجرا شدن این دستورات را دارد
با استفاده از متد fetch_assoc رکوردهای درخواستی به صورت آرایه ی انجمنی(اندیس رشته ای) برگردانده میشود.
متدهای دیگر نیز به تفکیک به صورت زیر هستند:
</div>

- insert : وظیفه ی افزودن رکوردها را به عهده دارد
- first : اولین رکورد انتخابی را در صورت موجود بودن برمیگرداند
- connection : ارتباط کنونی(با پایگاه داده) را برمیگرداند
- close : ارتباط کنونی (با پایگاه داده) را قطع میکند
# ورود به سیستم :

<div dir="rtl">
عمل لاگین یا ورود به سیستم توسط تابع زیر پیاده سازی شده است :
</div>

``` php
function login($studentNumber, $password){
	$url = "http://"."172.17.0.1".":8080/student/login?studentNumber="."$studentNumber"."&password="."$password";

	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => $url,
		CURLOPT_USERAGENT => 'Codular Sample cURL Request'
	));
	$resp = curl_exec($curl);
	// Close request to clear up some resources
	curl_close($curl);
	$r = (json_decode($resp, true));

	$tmp = $r['result'];
	if($r['status'] == 200 ){
	 return $tmp['session'];
	}
}
```
<div 
این تابع شماره دانشجویی و کلمه ی عبور را به عنوان پارامتر ورودی میپذرید و سپس با ارسال درخواست به ادرس زیر ,عملیات ورود به سیستم را انجام میدهد
میدهد:
ادرس وب سرویس به صورت زیر است
``` url
http://"."172.17.0.1".":8080/student/login
```
شماره دانش اموزش و کلمه عبور به صورت کوئری استرینگ (query string)به این ادرس اظافه میشوند و سپس درخواست ورود با استفاده از کتابخانه ی Curl انجام میشود.
پاسخ دریافتی از با فرمت json است و نیاز به دیکد کردن دارد.
با استفاده از تابع json_decode پاسخ دریافتی را به ارایه تبدیل میکنیم تا قابل استفاده شود.
در صورتی که عمل لاگین با موفقیت انجام شود سشن برگردانده شده را میتوان در مراحل بعدی به منظور تایید هویت به سرور ارسال کرد و مورد استفاده قرار داد.
## تایید هویت:
<div dir="rtl">
عمل تایید هویت با استفاده از تابع زیر پیاده سازی شده است.
</div>

``` php
function authenticate($studentNumber, $session){
  	$url = "http://"."172.17.0.1".":8080/authenticate?studentNumber="."$studentNumber"."&session="."$session";
  	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => $url,
		CURLOPT_USERAGENT => 'Codular Sample cURL Request'
	));
	$resp = curl_exec($curl);
	// Close request to clear up some resources
	curl_close($curl);
	$r = (json_decode($resp, true));
	if($r['status'] == 200 ){
	 return true;
	}else{
	  return false;
	}
}
```
<div dir="rtl">
این تابع با استفاده از شماره دانشجویی و سشن (از مرحله ی لاگین دریافت میشود) جهت تایید هویت استفاده میکند در صورتی که تایید هویت با موفقیت انجام شود مقدار true برگردانده میشود
در اینجا session به صورت توکن برای تایید هویت استفاده میشد. 
</div>

## نمایش لیست غذاهای موجود به منظور رزرو:


<div dir="rtl">
عملیات رزو غذا توسط فایلreservation.php انجام میگیرد.
با استفاده از شماره ی غذا میتوان غذای مورد نظر را رزو کرد.
</div>

``` php
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
```
<div dir="rtl">
در دستورات بالا در صورتی که غذا موجود نباشد پیامی به این منظور نمایش داده میشود.
همچنین بعد از رزو غذا پیامی جهت موفقیت امیز بودن ثبت غذا نمایش داده میشود.
</div>

``` php
$db = Db::getInstance();
$record = $db->query("SELECT foodId,Name,DayOfWeek FROM food_reservation WHERE studentNumber='$studentNumber'");
foreach ($record as $item){
    echo "<tr class='table-row'>";
    foreach ($item as $key => $value){
        echo "<td>";
        echo $value;
        echo "</td>";
    }
}
```

<div dir="rtl">
دستورات بالا درون فایل reserved.php قرار دارند. با استفاده از این دستورات لیست غذاهای رزو شده ی کاربر نمایش داده میشوند.
در دستورات بالا غذاهای رزو شده ی کاربر با استفاده از دو حلقه ی تودرتو به صورت جدول نمایش داده میشوند.
	</div>
	
### تکنولوژی های استفاده شده در سیستم

<div dir="rtl">
تکنولوژی های استفاده شده این سیستم به صورت زیر هستند:
</div>

* [php] - یک زبان برنامه نویسی سمت سرور است
* [Bootstrap] - فریمورک بوت استرپ به منظور طراحی ظاهر سامانه
* [jQuery] - جهت تعامل با کاربر
