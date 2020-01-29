<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>Pars http://dailyillini.com/category/news/ </title>
</head>
<body>
<center>
    <h1>
        result pars site - http://dailyillini.com/category/news/ 
    </h1>
    <hr>
</center>
<div class="content">
    <div class="row mb-2">
        <?php
        require_once 'setting.php';
        $data = $pdo->query("SELECT * FROM news ORDER BY created_at ASC")->fetchAll();
        $i = 1;
        foreach ($data as $row):
            ?>
            <div class="col-md-12">
                <div class="row no-gutters border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                    <div class="col-md-8">
                        <div class="col p-4 d-flex flex-column position-static">
                            <h3 class="mb-0"><?= $row['h1'] ?></h3>
                            <div class="mb-1 text-muted"><?= $row['created'] ?></div>
                            <p class="card-text mb-auto"><?= $row['text'] ?></p>
                            <a href="<?= $row['url_page'] ?>"
                               class="stretched-link" <?php echo $i <= 3 ? 'style="color: red"' : '' ?>>Page news</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <?php if($row['url_img_in_site'] != 'no image!'):?>
                            <img src="<?= $row['url_img_in_site'] ?>" alt="" style="max-height: 400px;">
                        <?php else:?>
                            <img src="http://nitmeghalaya.in/nitmeghalaya/htmls/images/defaultuser.jpg" alt="No image" style="max-height: 400px;min-width: 550px;">
                        <?php endif;?>
                    </div>
                </div>
            </div>
            <?php $i++; ?>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>