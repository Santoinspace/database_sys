<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Data</title>
        <link rel="stylesheet" href="../CSS/data.css">
        <style>
            .form-container {
                display: flex;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
        </style>

    </head>
    <body>

        <form action='' method='post'>
        <div id="table">
            <h3 id='head'>请输入sql语句：</h3>
            <input type="text" name="sql" id='sql' value="" />
            <input type="submit" name="submit" id='submit' value="执行sql指令" onclick="flush()">
        </div>
        </form>
        <div class="form-container"> 
            <form action='' method='POST'>
                <div id="show_all_columns">
                    <input type="submit" name="show_all_columns" value="展示所有的列名">
                </div>
            </form>
            <form action='' method='POST'>
                <div id="show_all_tables">
                    <input type="submit" name="show_all_tables" value="展示所有的表的信息">
                </div>
            </form>
            <form action='' method='POST'>
                <div id="clear_screen">
                    <input type="submit" name="clear_screen" value="清除屏幕">
                </div>
            </form>
        </div>

        <?php 
            #echo "<script type='text/javascript'>fflush();</script>";
            #连接xmapp自带的数据库

            # 用于清空目前屏幕信息的函数
            function clearOutputBuffer(){
                echo " ";
            }

            # 用于展示一个sql指令执行后的表的所有信息
            function show($result){
                if ($result->num_rows > 0) {
                    echo "<table>";
                    echo "<tr>";
                    // 输出表头
                    $fieldInfo = $result->fetch_fields();
                    foreach ($fieldInfo as $field) {
                        echo "<th>" . $field->name . "</th>";
                    }
                    echo "</tr>";
                
                    // 输出数据行
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        foreach ($row as $value) {
                            echo "<td>" . $value . "</td>";
                        }
                        echo "</tr>";
                    }
                
                    echo "</table>";
                } else {
                    echo "表中没有数据";
                }
            }

            # 从一个sql指令中提取表名的正则表达式集合
            function extractTableNameFromQuery($query) {
                $pattern = '/FROM\s+(\w+)/i';
                preg_match($pattern, $query, $matches);
                if (isset($matches[1])) {
                    return $matches[1];
                }
            
                $pattern = '/UPDATE\s+(\w+)/i';
                preg_match($pattern, $query, $matches);
                if (isset($matches[1])) {
                    return $matches[1];
                }
            
                $pattern = '/DELETE\s+FROM\s+(\w+)/i';
                preg_match($pattern, $query, $matches);
                if (isset($matches[1])) {
                    return $matches[1];
                }
            
                $pattern = '/INSERT\s+INTO\s+(\w+)/i';
                preg_match($pattern, $query, $matches);
                if (isset($matches[1])) {
                    return $matches[1];
                }
            
                return null;
            }
            
            # 获取一个数据库中所有表名字的函数
            function getAllTableNames($link, $databaseName) {
                $tableNames = array();
                $result = mysqli_query($link, "SHOW TABLES FROM $databaseName");
                while ($row = mysqli_fetch_row($result)) {
                    $tableNames[] = $row[0];
                }
                return $tableNames;
            }
            
            # 获取一个表中所有列名字的函数
            function getTableColumnNames($link, $tableName) {
                $columnNames = array();
                $result = mysqli_query($link, "SHOW COLUMNS FROM $tableName");
                while ($row = mysqli_fetch_assoc($result)) {
                    $columnNames[] = $row['Field'];
                }
                return $columnNames;
            }

            echo "<br>";
            # 若清除屏幕的按钮被点击
            if(isset($_POST['clear_screen'])){
                clearOutputBuffer();
            }
            # 若展示所有表内容的按钮被点击
            if(isset($_POST['show_all_tables'])){
                $link = mysqli_connect('localhost','root','','bank_dbs',3306) or die("connection failed");
                mysqli_set_charset($link,'utf8');
                echo "<br><br>";
                $tableNames = getAllTableNames($link, 'bank_dbs');
                echo "以下是所有的表，以及表中的数据";
                // 遍历每个表并获取列名列表
                foreach ($tableNames as $tableName) {
                    echo "<br>";
                    echo "表名是：" . $tableName;
                    // 执行查询和展示表格的代码，使用提取到的 $tableName 变量
                    $sql = "SELECT * FROM $tableName";
                    try{
                        $result = $link->query($sql);
                        show($result);
                    }
                    catch(Exception $e){
                        echo '<script>alert("cuowu")</script>';
                    }
                }
                // 关闭数据库连接
                mysqli_close($link);
            }
            # 若展示所有表的所有列的名字的按钮被点击
            if (isset($_POST['show_all_columns'])) {
                // 连接数据库
                $link = mysqli_connect('localhost','root','','bank_dbs',3306);
                mysqli_set_charset($link,'utf8');
                // 获取所有表的名字列表
                $tableNames = getAllTableNames($link, 'bank_dbs');
            
                // 遍历每个表并获取列名列表
                foreach ($tableNames as $tableName) {
                    $columnNames = getTableColumnNames($link, $tableName);
            
                    // 输出表名和列名
                    echo "表 $tableName 的列名：";
                    foreach ($columnNames as $columnName) {
                        echo $columnName . ", ";
                    }
                    echo "<br>";
                }
            
                // 关闭数据库连接
                mysqli_close($link);
            }
            # 若用户输入sql语句并点击执行指令的按钮
            if(isset($_POST["sql"])){
                $query = $_POST["sql"];
                if($query==null){
                    echo '<script>alert("输入为空")</script>';
                }else{
                    $link = mysqli_connect('localhost','root','','bank_dbs',3306) or die("connection failed");
                    mysqli_set_charset($link,'utf8');
                    echo "<br><br>";

                    #输出所输入的查询语言
                    $sql_String = strval($_POST["sql"]);
                    $sql_String = "\"".$sql_String."\"";
                    echo "输入的sql语句为：".$sql_String;
                    #尝试在数据库中进行sql查询

                    $tableName = extractTableNameFromQuery($query);

                    try{
                        $result = $link->query($query);
                    }
                    catch(Exception $e){
                        echo '<script>alert("数据操作失败，请检查输入格式")</script>';
                    }
                    
                    if ($tableName != null) {
                        echo "用户修改的表名是：" . $tableName;
                        // 执行查询和展示表格的代码，使用提取到的 $tableName 变量
                        $sql = "SELECT * FROM $tableName";
                        try{
                            $result = $link->query($sql);
                            show($result);
                        }
                        catch(Exception $e){
                            echo '<script>alert("cuowu")</script>';
                        }
                        
                    } 
                    else {
                        echo '<script>alert("未找到表名")</script>';
                    }
                }
                // 关闭数据库连接
                mysqli_close($link);
            }
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
    function fflush(){
        var input = document.querySelector('input');
        window.addEventListener('load', function() {
        input.value = "";
})
    }
</script>