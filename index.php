<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
header('Content-Type:text/html;charset=utf-8');
$script_start = microtime(1);
$sphinx = new SphinxClient();
//phpinfo();
$sphinx->SetServer('127.0.0.1', 9312);
//$sphinx->setMatchMode(SPH_MATCH_PHRASE);
$keywords = empty($_GET['keywords']) ? '四哥' : $_GET['keywords'];
//$sphinx->setLimits(0, 1000); //最多匹配1000条记录
$result = $sphinx->query($keywords, '*');
$list = array();
$mysql_start = $mysql_end = 0;
if (!empty($result['matches'])) {
    $ids = array_keys($result['matches']);
    $ids = implode(',', $ids);
    $options = array(
        pdo::MYSQL_ATTR_INIT_COMMAND => 'set names utf8',
    );
    $pdo = new PDO('mysql:host=localhost;dbname=csft', 'root', '123456',$options);
    $sql = "SELECT p.id,p.title,p.content,p.ctime,m.name FROM post p left join member m on p.author_id=m.id WHERE p.id in ($ids)";
    $mysql_start = microtime(1);
    $stmt = $pdo->query($sql);
    $mysql_end = microtime(1);
    $rst = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $opts = array(
        'before_match' => '<span style="color:red;">',
        'after_match' => '</span>',
    );

    foreach ($rst as $key => $value) {
        $list[] = $sphinx->buildExcerpts($value, 'main', $keywords, $opts);
    }
}
$script_end = microtime(1);
echo '<table>';
echo '<tr><td>共检索到</th><td>' . (empty($result['total']) ? 0 : $result['total']) . '篇文档</td></tr>';
echo '<tr><td>coreseek耗时</td><td>' . $result['time'] . '秒</td></tr>';
echo '<tr><td>mysql查询耗时</td><td>' . ($mysql_end - $mysql_start) . '秒</td></tr>';
echo '<tr><td>PHP共耗时</th><td>' . ($script_end - $script_start) . '秒</td></tr>';
echo '</table>';
echo '<hr />';
?>

<html>
    <head>
        <title>搜索</title>
        <style type="text/css">
            .section{
                border: 1px dotted gray;
                margin-bottom: 1em;
                padding:5px;
            }
            .title{
                /*background:gray;*/
                padding:5px;
            }
            .content{
                background: lightyellow;
                padding:5px;
            }
            .author{
                padding: 5px;
                background: lightseagreen;
            }
            a{
                color:blue;
            }
            .tips{font-size: 16px;color: red;}

        </style>
    </head>
    <body>
        <form>
            <input type="text" name="keywords" value="<?php echo $keywords; ?>"/>
            <input type="submit" value="搜索" />
        </form>
        <?php if ($list): ?>
            <?php foreach ($list as $item): ?>
                <div class="section">
                    <div class="title"><a href="show.php?id=<?php echo $item[0]; ?>"><?php echo $item[1]; ?></a></div>
                    <div class="content"><?php echo $item[2]; ?></div>
                    <div class="author">作者：<?php echo $item[4] . ' ' . $item[3]; ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="tips">你要查找的内容离家出走了，或者还没有出生！</div>
        <?php endif; ?>
    </body>
</html>