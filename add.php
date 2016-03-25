<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
header('Content-Type:text/html;charset=utf-8');
$options = array(
    pdo::MYSQL_ATTR_INIT_COMMAND => 'set names utf8',
);
$pdo = new PDO('mysql:host=localhost;dbname=csft', 'root', '123456', $options);
$sql = "SELECT id,name FROM member";
$stmt = $pdo->query($sql);
$authors = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_POST) {
    $title = htmlspecialchars($_POST['title']);
    $content = htmlspecialchars($_POST['content']);
    $author_id = $_POST['author_id'] - 0;
    $sql = "INSERT INTO post (title,content,author_id) values ('$title','$content',$author_id)";

    $pdo->exec($sql);
}
?>

<html>
    <head>
        <title>新增新闻</title>
        <style type="text/css">
            textarea{
                resize: none;
                width: 600px;
                height: 200px;
            }
            th{width:80px;}
        </style>
    </head>
    <body>
        <a href="index.php?keywords=四哥">搜索页面</a>
        <form method="post">
            <table>
                <tr>
                    <th>标题</th>
                    <td><input type="text" name="title"/></td>
                </tr>
                <tr>
                    <th>内容</th>
                    <td><textarea name="content"></textarea></td>
                </tr>
                <tr>
                    <th>作者</th>
                    <td>
                        <select name="author_id">
                            <?php foreach($authors as $author): ?>
                            <option value="<?php echo $author['id']; ?>"><?php echo $author['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td><input type="submit" value="新建" /></td>
                </tr>

            </table>
        </form>
    </body>
</html>