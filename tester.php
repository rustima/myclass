<?php
spl_autoload_register("myload");
function myload($class){
    include "$class.php";
}


//测试myPDO
$pdotest = new myPDO();
$pdotest->conn();
$userid = "9999";
$table = "member";
$userpwd = "abc123";
if($pdotest->ismem($userid,$table)){
    $pdotest->delete($userid);
    echo "原信息已删除<br />";
}
$pdotest->addmember($userid, "tester", "abc123", "tttskrskr");
$pdotest->changepwd($userid, "abc123", "aaa321", $table);
$info = $pdotest->getInfoById($userid, $table);
// var_dump($info);
echo "<body><a>name:{$info[0]['name']}</a><br /><a>detail:{$info[0]['detail']}</a></body>";