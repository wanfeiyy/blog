<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
header('Content-Type:text/html;charset=utf-8');

$id = empty($_GET['id']) ? 0 : $_GET['id'] - 0;

$options = array(
    pdo::MYSQL_ATTR_INIT_COMMAND => 'set names utf8',
);
$pdo = new PDO('mysql:host=localhost;dbname=csft', 'root', '123456', $options);
$sql = "SELECT p.id,p.title,p.content,p.ctime,m.name FROM post p left join member m on p.author_id=m.id WHERE p.id='$id'";
$stmt = $pdo->query($sql);
$rst = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<html>
    <head>
        <title>查看详细信息</title>
        <style type="text/css">
            table{border-collapse: collapse;border:1px solid gray;padding: 5px;}
            td,th{border:1px solid gray;padding: 5px;}
            .error{font-size: 16px;color: red;}
            th{width: 80px;}
            td{width:600px;}
        </style>
    </head>
    <body>
        <a href="index.php?keywords=四哥">搜索页面</a> |
        <a href="add.php">新建页面</a>
        <?php if ($rst): ?>
        <table>
                <tr>
                    <th>ID</th>
                    <td><?php echo $rst['id']; ?></td>
                </tr>
                <tr>
                    <th>标题</th>
                    <td><?php echo $rst['title']; ?></td>
                </tr>
                <tr>
                    <th>内容</th>
                            <td><?php echo nl2br($rst['content']); ?></td>
                    <tr>
                    <th>作者</th>
                    <td><?php echo $rst['name']; ?></td>
                </tr>
                <tr>
                    <th>创建时间</th>
                    <td><?php echo $rst['ctime']; ?></td>
                </tr>
            </table>

        <?php else: ?>
            <div class="error">查无此记录</div>
        <?php endif; ?>
    </body>
</html>
