<html>

<head>
    <title>title</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" type="text/css" href="css/estil.css" />
</head>

<body>
    <?php
    include 'classes/Mysqli.php';
    include 'classes/PDO.php';
    include 'classes/Adodb.php';
    include 'classes/Odbc.php';
    include 'classes/SQLServer.php';
    if (isset($_GET['used'])) {
        $selected = $_GET['used'];
    }

    $boto = 'insereix';
    $boto2 = 'insereix Llibre';
    $autor = null;
    $titol = '';
    $any = '';

    if (isset($_POST['send'])) {
        $selected = $_POST['objects'];
        header('Location: http://localhost/conectors?used=' . $selected);
    }
    $possible_connections = array('Mysqli', 'Adodb', 'Pdo', 'SQLServer');

    echo "<h3>Selecciona una connexió de BD</h3>";
    echo '<form method="post" name="f1">
    <select name="objects">';

    for ($i = 0; $i < count($possible_connections); $i++) {
        if ($i == $selected) {
            echo '<option value="' . $i . '" selected>' . $possible_connections[$i] . '</option>';
            continue;
        }
        echo '<option value="' . $i . '">' . $possible_connections[$i] . '</option>';
    }

    echo '</select><br><br>
    <input type="submit" name="send" value="Selecciona"/>
</form><hr>';

    //en cas de que seleccioni metode mostrar la parafernalia

    if (isset($_GET['used'])) {
        $nom = '';
        $dNaix = '';

        $selected = $_GET['used'];
        switch ($selected) {
            case 0:
                $obj = new Mysqli2();
                break;
            case 1:
                $obj = new Adodb2();
                break;
            case 2:
                $obj = new PDO2();
                break;
            case 3:
                $obj = new sqlsrv();
                break;
            default:
                header("Refresh:0");
        }

        if (isset($_GET['dA'])) {
            $obj->deleteAutor($_GET['dA']);
            header('Location: http://localhost/conectors?used=' . $selected);
        }

        if (isset($_GET['eA'])) {

            $dades = $obj->getOne($_GET['eA']);
            $boto = 'edita';
            if ($dades['nom'] != null && $dades['data_naix'] != null) {
                $nom = $dades['nom'];
                $dNaix = $dades['data_naix'];
            } else {
                header('Location: http://localhost/conectors?used=' . $selected);
            }
        }

        if (isset($_POST['ins2'])) {
            if (!isset($_GET['eL'])) {
                $data = array(
                    'id_autor' => $_POST['autors'],
                    'titol' => $_POST['titol'],
                    'any' => $_POST['any']
                );
                $obj->insertLlibre($data);
            } else {
                $data = array(
                    'id' => $_GET['eL'],
                    'id_autor' => $_POST['autors'],
                    'titol' => $_POST['titol'],
                    'any' => $_POST['any']
                );
                $obj->editLlibre($data);
            }
            header('Location: http://localhost/conectors?used=' . $selected);
        }

        if (isset($_GET['eL'])) {
            $dades = $obj->getLlibre($_GET['eL']);

            $autor = $dades['autor'];
            $titol = $dades['titol'];
            $any = $dades['any'];

            $boto2 = 'Edita';
        }

        if (isset($_POST['ins'])) {
            if (isset($_GET['eA'])) {
                $data = array(
                    'id' => $_GET['eA'],
                    'nom' => $_POST['nom'],
                    'data_naix' => $_POST['dnaix']
                );

                $obj->editAutor($data);
            } else {
                $data = array(
                    'nom' => $_POST['nom'],
                    'data_naix' => $_POST['dnaix']
                );
                $obj->insertAutor($data);
            }

            header('Location: http://localhost/conectors?used=' . $selected);
        }

        //dibuixar una taula
        echo "<h2 class='centrar'>Taula autors - SGBD utilitzat: " . $possible_connections[$_GET['used']] . " </h2>";
        echo "<div class='info-container'>";
        $obj->showAutors();
        if (!isset($_GET["autor"])) {
            $obj->showLlibres();
        } else {
            $obj->showLLibreAutor($_GET["autor"]);
        }
        echo "</div>";
        echo '<div class="insert-container">
        <div>
            <h2>Inserció / Edició d\'autor</h2>
            <form method="post" action="#" name="f2">
                Nom: <input type="text" value="' . $nom . '" name="nom" /><br>
                Data Naixement: <input type="text" value="' . $dNaix . '" name="dnaix" /><br>
                <input type="submit" value="' . $boto . '" name="ins" />
            </form> 
        </div>   
        <div>
            <h2>Inserció / Edició de llibres</h2>
            <form method="post" action="#" name="f2">
            Autor: ';
        $obj->selectAutor($autor);
        echo '<br>
                Titol: <input type="text" value="' . $titol . '" name="titol" /><br>
                Any publicació: <input type="text" value="' . $any . '" name="any" /><br>
                <input type="submit" value="' . $boto2 . '" name="ins2"/>
            </form> 
        </div>
    </div>
    ';
    }
    ?>
</body>

</html>