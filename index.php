<?php
  require_once 'config.php';
  require_once 'functions.php';

  // ログイン済みかどうかのチェックを行う
  // isSignin();

  // デーテベースへ接続
  $ditter_db = connectDb();

  // ユーザのIDを代入
  $user_id = '1';

  // ユーザのIDから各種データを取得
  $user_data = getUserData($ditter_db, $user_id);
  $screen_name = $user_data['screen_name'];
  $user_name = $user_data['user_name'];
  $comment = $user_data['comment'];

  // POSTリクエストが有った場合
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $post_text = $_POST['post_text'];
      print $post_text;

    // 入力文字数のチェック
    if (mb_strlen($post_text) > 140) {
        $error = '文字数が140文字を超えています';
    } else {
        writePost($ditter_db, $user_id, $post_text);

      // 二重投稿防止のため再読み込み
      header('Location: '.$_SERVER['REQUEST_URI']);
    }
  }

  // ページング
  $current_page = empty($_GET['page']) ? 1 : $_GET['page'];
  $next_page = $current_page + 1;
  $prev_page = $current_page - 1;
  $table_name = 'posts';
  $record_num = recordCounter($ditter_db, $table_name);
  $show_limit_per_page = 5;
  $page_limit = ceil($record_num / $show_limit_per_page);

  // タイムラインに表示する投稿の取得
  $start_at = ($current_page - 1) * 5;
  $posts = getTimeline($ditter_db, $start_at, $show_limit_per_page);
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ditter</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
</head>

<body>
  <!-- Fixed navbar -->
  <style>
    body {
      padding-top: 70px;
    }
  </style>
  <nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="/">Ditter</a>
      </div>
      <div id="navbar" class="navbar-collapse collapse">
        <ul class="nav navbar-nav">
          <li class="active"><a href="/">ホーム</a></li>
          <li><a href="/reply.php">返信</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <li>
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary navbar-btn" data-toggle="modal" data-target="#postModal">
              <span class="glyphicon glyphicon-comment" aria-hidden="true"></span> 投稿
            </button>
          </li>
          <li>
            <a href="<?php $_SERVER['REQUEST_URI'] ?>"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> 更新</a>
          </li>
          <li>
            <a href="#"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> ログアウト</a>
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
          <form action="" method="post">
            <div class="form-group">
              <label for="post_text" class="control-label">メッセージ（140字まで）：</label>
              <textarea class="form-control" id="post_text" name="post_text" maxlength="140"></textarea>
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
          <form action="" method="post">
            <div class="form-group">
              <label for="reply_text" class="control-label">メッセージ（140字まで）：</label>
              <textarea class="form-control" id="reply_text" name="post_text" maxlength="140"></textarea>
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
            <?php echo "$error"; ?>
          </p>
        </div>
        <?php elseif (!empty($_POST)): ?>
        <div class="alert alert-success" id="flush">
          <p>
            <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
            <span class="sr-only">Success:</span>
            <?php echo '投稿しました'; ?>
          </p>
        </div>
        <?php endif; ?>
        <div class="panel panel-default visible-xs">
          <div class="panel-heading">
            <h3 class="panel-title">新規投稿</h3>
          </div>
          <div class="panel-body">
            <form>
              <div class="form-group">
                <textarea class="form-control" id="message-text"></textarea>
              </div>
              <button type="submit" class="btn btn-primary">投稿</button>
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
          <?php foreach ($posts as $key => $value):
            $post_by = getUserData($ditter_db, $value['user_id']);
          ?>
            <li class="list-group-item">
              <div class="container-fluid">
                <h5>
                  <?php print $post_by['user_name'] ?>
                </h5>
                <p class="small text-muted reply-to">@
                  <?php print $post_by['screen_name'] ?>
                </p>
                <p>
                  <?php print $value['text'] ?>
                </p>
                <p class="small">
                  <?php print $value['created_at'] ?>
                </p>
                <p class="text-right">
                  <button type="button" class="btn btn-primary reply-btn" data-toggle="modal" data-target="#replyModal">
                    <span class="glyphicon glyphicon-send" aria-hidden="true"></span>　返信する
                  </button>
                  <?php if ($user_id == $post_by['id']): ?>
                  <button type="button" class="btn btn-danger reply-btn">
                    <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>　削除する
                  </button>
                  <?php endif;?>
                </p>
              </div>
            </li>
          <?php endforeach; ?>
          </ul>
        </div>
        <div class="container-fluid text-center">
          <nav>
            <ul class="pager">
            <?php if ($current_page > 1): ?>
              <li class="previous">
                <?php print '<a href="?page='.$prev_page.'">' ?>
                  <span aria-hidden="true">&larr;</span> Newer
                </a>
              </li>
            <?php endif ?>
            <?php if ($current_page != $page_limit): ?>
              <li class="next">
                <?php print '<a href="?page='.$next_page.'">' ?>
                  Older
                  <span aria-hidden="true">&rarr;</span>
                </a>
              </li>
            <?php endif ?>
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
              <?php print $user_name; ?>
            </h4>
            <p class="small text-muted">@
              <?php print $screen_name; ?>
            </p>
            <p>
              <?php print $comment; ?>
            </p>
          </div>
          <div class="panel-footer"><a href="#">ユーザ情報を変更する</a></div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  <script type="text/javascript">
    $(function() {
      // #flush の要素を7秒でフェードアウト
      $('#flush').fadeOut(7000);

      // リプライ時にスクリーンネームを埋めておく
      $('.reply-btn').click(function() {
        var $screen_name = $(this).parent().siblings('.reply-to').text();
        $('#reply_text').val($screen_name + ' ');
      });
    });
  </script>
</body>

</html>
