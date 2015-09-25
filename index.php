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
            <button type="button" class="btn btn-primary navbar-btn" data-toggle="modal" data-target="#myModal">
              <span class="glyphicon glyphicon-comment" aria-hidden="true"></span> 投稿
            </button>
          </li>
        </ul>
      </div>
      <!--/.nav-collapse -->
    </div>
  </nav>
  <!-- Modal -->
  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title" id="myModalLabel">新規投稿</h4>
        </div>
        <div class="modal-body">
          <form>
            <div class="form-group">
              <label for="message-text" class="control-label">メッセージ（140字まで）：</label>
              <textarea class="form-control" id="message-text"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">投稿する</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- Main -->
  <div class="container">
    <div class="row">
      <div class="col-sm-8 col-sm-push-4">
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
        <div class="panel panel-default">
          <!-- Default panel contents -->
          <div class="panel-heading">
            <h3 class="panel-title">みんなの投稿</h3>
          </div>
          <!-- List group -->
          <ul class="list-group">
            <li class="list-group-item">
              <div class="container-fluid">
                <h5>ローム記念館プロジェクトDIT</h5>
                <p class="small text-muted">@dit</p>
                <p>
                  DHacksが無事終了しました。協賛企業の方々、先生方、ローム記念館事務の方、 そして参加者のみなさん、本当にありがとうございました！ 参加者のみなさんの今後の活躍を期待しております！
                </p>
                <p class="small">
                  8:51 PM - 11 Sep 2015
                </p>
              </div>
            </li>
            <li class="list-group-item">
              <div class="container-fluid">
                <h5>ローム記念館プロジェクトDIT</h5>
                <p class="small text-muted">@dit</p>
                <p>
                  DHacksが無事終了しました。協賛企業の方々、先生方、ローム記念館事務の方、 そして参加者のみなさん、本当にありがとうございました！ 参加者のみなさんの今後の活躍を期待しております！
                </p>
                <p class="small">
                  8:51 PM - 11 Sep 2015
                </p>
              </div>
            </li>
          </ul>
        </div>

        <div class="container-fluid text-center">
          <nav>
            <ul class="pager">
              <li class="previous">
                <a href="#">
                  <span aria-hidden="true">&larr;</span> Older</a>
              </li>
              <li class="next"><a href="#">Newer <span aria-hidden="true">&rarr;</span></a></li>
            </ul>
          </nav>
        </div>
      </div>
      <div class="col-sm-4 col-sm-pull-8">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">ユーザ情報</h3>
          </div>
          <div class="panel-body">
            <h4 class="leader">ローム記念館プロジェクトDIT</h4>
            <p class="small text-muted">@dit</p>
            <p>同志社ローム記念館プロジェクトDIT（Doshisha Institute of Technology）です。プログラミングを学べる勉強会やハッカソンを開催します。web制作・アプリ開発に興味のある方は気軽にご連絡ください。</p>
          </div>
          <div class="panel-footer"><a href="#">ユーザ情報を変更する</a></div>
        </div>
      </div>
    </div>
  </div>
  </div>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>

</html>
