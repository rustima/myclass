<?php
/*
 * 轻量级PDO，包含对用户信息的增删改操作
 * 
 * @author rustima
 * @version 0.8
 * 
 */

class myPDO{
    private $hostname = "";
    private $user = "";
    private $pwd = "";
    private $db = "";
    private $conn = "";
    
    /*
     * @构造函数
     * @param string $hostname
     * @param string $user
     * @param string $pwd
     * @param string $db
     * 
     */
    final public function __construct($hostname = "localhost",$user = "root",$pwd = "123456",$db = "test"){
        $this->hostname = $hostname;
        $this->user = $user;
        $this->pwd = $pwd;
        $this->db = $db;
    }
    
    /*
     * @打开数据库连接
     * @return mixed 成功返回连接对象，否则返回FALSE
     * 
     */
    public function conn(){
        $this->conn = new mysqli($this->hostname,$this->user,$this->pwd,$this->db);
//         $this->conn->query("SET NAMES UTF8");  
        $this->conn->set_charset("utf8");  //设置编码utf8
//         var_dump($this->conn);
        if(!mysqli_connect_error()){
        return $this->conn;
        }else{
            echo "conn error:".mysqli_connect_errno().":".mysqli_connect_error();
            return FALSE;
        }
    }
    
    /*
     * @添加用户
     * 
     * @param string $userid
     * @param string $userpwd
     * @param string $username
     * @param string $detail
     * @return boolean 成功返回TRUE，否则错误信息和返回FALSE
     */
    public function addmember($userid,$username,$userpwd,$detail){
        $this->query("INSERT INTO `member` (`id`, `name`, `pwd`, `detail`) VALUES ('$userid', '$username', '$userpwd', '$detail')");
        if($this->conn->error){
            echo "MYSQL添加字段时错误: ".$this->conn->error;
            return FALSE;
        }else
        return TRUE;
    }
    
    
    /*
     * @修改密码
     * 
     * @param string $userid
     * @param string $o_userpwd 旧密码
     * @param string $n_userpwd 新密码
     * @param string $table
     * @return boolean 成功返回TRUE，否则错误信息和返回FALSE
     */
    public function changepwd($userid,$o_userpwd,$n_userpwd,$table){
        if($this->verify($userid, $o_userpwd, $table)){ //校验旧密码是否正确
            $a = $this->query("UPDATE $table SET pwd = '$n_userpwd' WHERE $table.id = '$userid'");
            if($a){
                return TRUE;
            }else{
                echo "修改密码失败: ".$this->conn->error;
                return FALSE;
                }
        }else{
//             echo "userid==$userid,o_userpwd==$o_userpwd,table==$table";
//             var_dump($this->verify($userid, $o_userpwd, $table));
            echo "id或密码错误!";
            return FALSE;
        } 
    }
    
    /*
     * @用户密码验证
     * @param string $userid 待验证的ID
     * @param string $pwd 待验证的密码
     * @param string $table 表名
     * @return boolean 密码正确返回TRUE，否则返回FALSE
     * 
     */
    public function verify($userid,$userpwd,$table){
        $pwd = $this->getpwd($userid, $table);       //从密码获取id
        if($pwd){
            if($pwd===$userpwd){
                return TRUE;
            }else{
                echo "密码错误！";
                return FALSE;
            }
        }else{
            echo "获取密码失败！".$this->conn->error;
            return FALSE;
        }
    }
    
    
    /*
     * @删除指定用户
     * @param string @userid
     * @return boolean 成功返回TRUE，否则错误信息和返回FALSE
     *
     */
    
    public function delete($userid){
        $this->query("DELETE FROM member WHERE member.id = '$userid'");
        if($this->conn->error){
            echo "MYSQL删除字段时错误:".$this->conn->error;
            return FALSE;
        }
        else {
            return TRUE;
        }
    }
    
    /*
     * @查询是否存在指定id的用户
     * @param string $userid 
     * @param string $table
     * @return boolean 成功返回TRUE，否则返回FALSE
     */
    public function ismem($userid,$table){
        if($this->query("select * from $table where id = $userid")){
            return TRUE;
        }else return FALSE;
    }
    
    
    /*
     * @返回指定ID的密码
     * @param string $userid 获取id
     * @param string $table 表名
     * @return mixed 返回密码或FALSE
     *
     */
    private function getpwd($userid,$table){
        $pwd = $this->query("select pwd from $table where id = '$userid'");
        if($pwd){
            $pwd = implode($pwd[0]);
            return $pwd;
        }else{
            return;
        }
    }
    
    /*
     * @由密码返回ID
     * @param string $pwd 密码
     * @param string $table 表名
     * @return mixed 返回ID或FALSE
     * 
     */
    private function getid($pwd,$table){
        $id = $this->query("select id from $table where pwd = '$pwd'");
        if($id){
            return $id;
        }else{
            return;
        }
        
    }
    
    /*
     * @获取指定ID的名字和个人信息
     * @param $userid 指定ID
     * @param $table 表名
     * @return mixed 成功返回结果集，否则输出错误信息和返回FALSE
     * 
     */
    
    public function getInfoById($userid,$table){
        $result = $this->query("select name,detail from $table where $table.id = '$userid'");
        if($result){
//             var_dump($result);
            return $result;
        }else{
            echo "获取信息失败".$this->conn->error;
            return FALSE;
        }
    }
    
    
    
    /*
     * @执行语句
     * @param string $query
     * @return mixed 成功返回结果集数组或TRUE，否则返回FALSE
     *
     */
    private function query($query){
        $result = $this->conn->query($query);  //$result根据语句有可能为对象和布尔值真
        if(is_object($result)){ //判断是否对象
            foreach($result as $result){
                $arr[] = $result;
            }
//             var_dump($arr);
            return $arr;
        }else if($result==TRUE){ //判断是否布尔值真
            return TRUE;
        }
        else
        {
            echo "query error";
            return FALSE;
        }
        
    }
    
    
    /*
     * @关闭连接
     * 
     */
    public function close(){
        mysqli_close($this->conn);
    }
    
}