<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Data</title>
        <link rel="stylesheet" href="../CSS/data.css">
    </head>
    <body>
        <form action='' method='post'>
        <div id="table">
            <h3 id='head'>请输入sql语句：</h3>
            <input type="text" name="sql" id='sql' value="" />
            <input type="submit" name="submit" id='submit' value="查询" onclick="flush()">
        </div>
        </form>
        <?php 
            $link = mysqli_connect('localhost','root','','test',3306) or die("connection failed");
            mysqli_set_charset($link,'utf8');
            echo "<br><br>";
            $col = mysqli_query($link,"SHOW COLUMNS FROM table1");
            echo "这是所有的属性：";
            while($colName = mysqli_fetch_assoc($col)){
                echo $colName['Field'];
                echo " ";
            }
            echo "<br>";

            echo "<br>";
            #output all the row in the table "table1"
            $sql = mysqli_query($link,"select *from table1");
            $message = array($sql);
            while($row = mysqli_fetch_row($sql))
            {
                print_r($row);
                echo "<br>";
                #$message[] = $row;
            }
            $result = false;

            if(isset($_POST["sql"])){
                $query = $_POST["sql"];
                if($query==null){
                    echo '<script>alert("输入为空")</script>';
                }else{
                    
                    $sql_String = strval($_POST["sql"]);
                    $sql_String = "\"".$sql_String."\"";
                    echo "输入的sql语句为：".$sql_String;
                    try{
                        $result = $link->query($query);
                    }
                    catch(Exception $e){
                        echo '<script>alert("数据操作失败，请检查输入格式")</script>';
                    }
                    if($result){
                        echo "<br>以下为所有sql查询结果：";
                        echo "<br>";
                        $result2 = mysqli_query($link,$query);
                        while($row = mysqli_fetch_row($result2))
                        {
                            print_r($row);
                            echo "<br>";
                            #$message[] = $row;
                        }
                    }
                }
                #$message = array($sql);
                #while($row = mysqli_fetch_row($sql))
                #{
                #    print_r($row);
                #    echo "<br>";
                #}
            }
            
            #$sql = mysqli_query($link,"insert into table1(time,place,boy,girl) VALUES(21,22,23,24)");
            
            


            #print_r($message);
            #$datarow = mysqli_num_rows($sql);

            #for($i=0;$i<$datarow;$i++){
            #    $sql_arr = mysqli_fetch_assoc($sql);
            #    $time = $sql_arr['time'];
            #    $place = $sql_arr['place'];
            #    $boy = $sql_arr['boy'];
            #    $girl = $sql_arr['girl'];
            #    echo "<tr><td>$time </td><td>$place </td><td>$boy </td><td>$girl </td></tr>";
            #    echo "<br>";
            #}
            
		?>

    </body>
</html>
<script type="text/javascript">
    function getValue(id){
        return document.getElementById(id).value;
    }
    function passQuery(){
        getValue('sql');
    }
</script>