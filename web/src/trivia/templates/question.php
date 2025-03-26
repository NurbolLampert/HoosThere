<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
  <head>
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="CS4640 Spring 2025">
    <meta name="description" content="Our Front-Controller Trivia Game">  
    <title>PHP Form Example - Trivia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"  integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"  crossorigin="anonymous">       
  </head>
  <body>
    <div class="container" style="margin-top: 15px;">
      <div class="row">
        <div class="col-xs-12">
          <h1>Trivia Game</h1>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <?=$message?>
        </div>
      </div>
      <div class="row">
        <div class="col-8">
          <div class="card">
            <div class="card-header">
              Question
            </div>
            <div class="card-body">
            <h5 class="card-title"><?=$question["question"]?></h5>
            </div>
          </div>
          <div class="m-4">
            <form action="?command=answer" method="post">
              <input type="hidden" name="qid" value="<?=$question["id"]?>"> 
              <div class="mb-3">
                <label for="answer" class="form-label">Trivia Answer: </label>
                <input type="text" class="form-control" id="trivia-answer" name="answer">
              </div>
              <button type="submit" class="btn btn-primary">Submit Answer</button>
              <a href="?command=logout" class="btn btn-danger">End Game</a>
            </form>
          </div>
        </div>
        <div class="col-4">
          <div class="card">
            <div class="card-header">
              Game Info
            </div>
            <div class="card-body">
              <p class="card-text">Score: <?=$score?></p> <!-- new! -->
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12">
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  </body>
</html>
