<?php
require_once 'init.php';

// ログイン済みかどうかのチェックを行う
if (!isSignin()) {
    $signin_url = 'signin.php';
    header("Location: {$signin_url}");
    exit;
}

// デーテベースへ接続
$db = connectDb();

// ユーザのIDを代入
$user_id = $_SESSION['user_id'];

// ユーザのIDから各種データを取得
try {
    $user_data = getUserData($db, $user_id);
    $screen_name = $user_data['screen_name'];
    $user_name = $user_data['user_name'];
    $comment = $user_data['comment'];
} catch (Exception $e) {
    print $e->getMessage();
}

// ページング
$current_page = empty($_GET['page']) ? 1 : $_GET['page'];
$next_page = $current_page + 1;
$prev_page = $current_page - 1;
$table_name = 'posts';
$record_num = postsCounter($db);
$show_limit_per_page = 5;
$page_limit = floor($record_num / $show_limit_per_page) + 1;

// POSTリクエストが有った場合
if (isset($_POST['postText'])) {
    $postText = $_POST['postText'];

    // 入力文字数のチェック
    if (mb_strlen($postText) > 140) {
        $error = '文字数が140文字を超えています';
    } else {
        writePost($db, $user_id, $postText);

        // 二重投稿防止のため再読み込み
        header('Location: '.$_SERVER['SCRIPT_NAME']);
        exit;
    }
}

// 削除機能
if (isset($_GET['delete_post_id'])) {
    $delete_post_id = $_GET['delete_post_id'];
    try {
        $delete_post_data = getPostData($db, $delete_post_id);
        if ($delete_post_data['user_id'] == $user_id) {
            deletePost($db, $delete_post_id);
        }

        // 再読み込み
        header('Location: '.$_SERVER['SCRIPT_NAME']);
        exit;
    } catch (Exception $e) {
        print $e->getMessage();
    }
}

// タイムラインに表示する投稿の取得
$start_at = ($current_page - 1) * 5;
$posts = getTimeline($db, $start_at, $show_limit_per_page);
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ditter</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 70px;
        }
    </style>
</head>
<body>
<!-- Fixed navbar -->
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href=".">Ditter</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href=".">ホーム</a></li>
                <li><a href="/reply.php">返信</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-primary navbar-btn" data-toggle="modal"
                            data-target="#postModal">
                        <span class="glyphicon glyphicon-comment" aria-hidden="true"></span> 投稿
                    </button>
                </li>
                <li>
                    <a href="<?php print $_SERVER['REQUEST_URI']; ?>"><span class="glyphicon glyphicon-refresh"
                                                                     aria-hidden="true"></span> 更新</a>
                </li>
                <li>
                    <a href="signout.php"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> ログアウト</a>
                </li>
            </ul>
        </div>
        <!--/.nav-collapse -->
    </div>
</nav>
<!-- postModal -->
<div class="modal fade" id="postModal" tabindex="-1" role="dialog" aria-labelledby="postModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="postModalLabel">新規投稿</h4>
            </div>
            <div class="modal-body">
                <form action="<?php print $_SERVER['SCRIPT_NAME']; ?>" method="post">
                    <div class="form-group">
                        <label for="postText1" class="control-label">メッセージ（140字まで）：</label>
                        <textarea class="form-control" id="postText1" name="postText" maxlength="140"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">投稿する</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- replyModal -->
<div class="modal fade" id="replyModal" tabindex="-1" role="dialog" aria-labelledby="replyModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="replyModalLabel">リプライ</h4>
            </div>
            <div class="modal-body">
                <form action="<?php print $_SERVER['SCRIPT_NAME']; ?>" method="post">
                    <div class="form-group">
                        <label for="replyText" class="control-label">メッセージ（140字まで）：</label>
                        <textarea class="form-control" id="replyText" name="postText" maxlength="140"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">返信する</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Main -->
<div class="container">
    <div class="row">
        <div class="col-sm-8 col-sm-push-4">
            <!-- フラッシュメッセージ -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" id="flush">
                    <p>
                        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                        <span class="sr-only">Error:</span>
                        <?php print "$error"; ?>
                    </p>
                </div>
            <?php elseif (!empty($_POST['postText'])): ?>
                <div class="alert alert-success" id="flush">
                    <p>
                        <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                        <span class="sr-only">Success:</span>
                        <?php print '投稿しました'; ?>
                    </p>
                </div>
            <?php endif; ?>
            <div class="panel panel-default visible-xs">
                <div class="panel-heading">
                    <h3 class="panel-title">新規投稿</h3>
                </div>
                <div class="panel-body">
                    <form action="<?php print $_SERVER['SCRIPT_NAME']; ?>" method="post">
                        <div class="form-group">
                            <textarea class="form-control" id="postText2" name="postText" maxlength="140" title=""></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">投稿する</button>
                    </form>
                </div>
            </div>
            <!-- 全員の投稿表示領域 -->
            <div class="panel panel-default">
                <!-- Default panel contents -->
                <div class="panel-heading">
                    <h3 class="panel-title">みんなの投稿</h3>
                </div>
                <!-- List group -->
                <ul class="list-group">
                    <?php if (!$posts): ?>
                        <li class="list-group-item">
                            <div class="container-fluid">
                                <p class="text-center">
                                    <strong>ツイートがありません。</strong>
                                </p>
                            </div>
                        </li>
                    <?php else: ?>
                        <?php foreach ($posts as $key => $value):
                            try {
                                $post_by = getUserData($db, $value['user_id']);
                            } catch (Exception $e) {
                                print $e->getMessage();
                            }
                            ?>
                            <?php if (isset($post_by)): ?>
                            <li class="list-group-item">
                                <div class="container-fluid">
                                    <h5><?php print escape($post_by['user_name']) ?></h5>

                                    <p class="small text-muted reply-to">@<?php print escape(
                                            $post_by['screen_name']
                                        ) ?></p>

                                    <p><?php print escape($value['text']) ?></p>

                                    <p class="small"><?php print $value['created_at'] ?></p>

                                    <p class="text-right">
                                        <button type="button" class="btn btn-primary reply-btn" data-toggle="modal"
                                                data-target="#replyModal">
                                            <span class="glyphicon glyphicon-send" aria-hidden="true"></span>　返信する
                                        </button>
                                        <?php if ($user_id == $post_by['id']): ?>
                                            <?php
                                            if ($current_page == 1) {
                                                if (isset($_GET['page'])) {
                                                    $delete_url = $_SERVER['REQUEST_URI'].'&delete_post_id='.$value['id'];
                                                } else {
                                                    $delete_url = $_SERVER['REQUEST_URI'].'?delete_post_id='.$value['id'];
                                                }
                                            } else {
                                                $delete_url = $_SERVER['REQUEST_URI'].'&delete_post_id='.$value['id'];
                                            }
                                            ?>
                                            <button type="button" class="btn btn-danger reply-btn" name="delete_post"
                                                    onclick="location.href='<?php print $delete_url ?>'">
                                                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>　削除する
                                            </button>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </li>
                        <?php endif ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="container-fluid text-center">
                <nav>
                    <ul class="pager">
                        <?php if ($current_page > 1): ?>
                            <li class="previous">
                                <a href="<?php print '?page='.$prev_page ?>">
                                    <span aria-hidden="true">&larr;</span> Newer
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ($current_page != $page_limit): ?>
                            <li class="next">
                                <a href="<?php print '?page='.$next_page ?>">
                                    Older
                                    <span aria-hidden="true">&rarr;</span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
        <div class="col-sm-4 col-sm-pull-8">
            <!-- ユーザ情報表示領域 -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">ユーザ情報</h3>
                </div>
                <div class="panel-body">
                    <h4 class="leader">
                        <?php if (isset($user_name)) {print $user_name;}; ?>
                    </h4>

                    <p class="small text-muted">@
                        <?php if (isset($screen_name)) {print $screen_name;}; ?>
                    </p>

                    <p>
                        <?php if (isset($comment)) {print $comment;}; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script>
    $(function () {
        // #flush の要素を7秒でフェードアウト
        $('#flush').fadeOut(7000);

        // リプライ時にスクリーンネームを埋めておく
        $('.reply-btn').click(function () {
            var $screen_name = $(this).parent().siblings('.reply-to').text();
            $('#reply_text').val($screen_name + ' ');
        });
    });
</script>
</body>

</html>
