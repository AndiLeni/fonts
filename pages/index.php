<?php

echo rex_view::title('Fonts');



// selected variants to download
$selected_variants = rex_post("selected_variants", "array", []);
if ($selected_variants != []) {
    $selected_font = rex_get("sel_font", "string", "");

    try {
        $socket = rex_socket::factory('google-webfonts-helper.herokuapp.com', '443', true);
        $socket->setPath('/api/fonts/' . $selected_font);

        $response = $socket->doGet();

        if ($response->isOk()) {
            $json = $response->getBody();
            $font = json_decode($json, true);
        } else {
            echo "Error";
            exit;
        }
    } catch (rex_socket_exception $e) {
        echo $e->getMessage();
        exit;
    }

    $font_saves = [];

    foreach ($font['variants'] as $fv) {
        if (in_array($fv['id'], $selected_variants)) {

            $filename = str_replace("'", "", $fv['fontFamily']) . '-' . $fv['id'] . '.woff2';

            $url = parse_url($fv['woff2']);

            try {
                $socket = rex_socket::factory($url['host'], '443', true);
                $socket->setPath($url['path']);

                $response = $socket->doGet();

                if ($response->isOk()) {
                    $body = $response->getBody();

                    file_put_contents(rex_path::addonAssets("fonts", $filename), $body);

                    $font_saves[] = [
                        "fontFamily" => $fv['fontFamily'],
                        "fontStyle" => $fv['fontStyle'],
                        "fontWeight" => $fv['fontWeight'],
                        "fileName" => $filename
                    ];
                } else {
                    echo "Error " . $filename;
                }
            } catch (rex_socket_exception $e) {
                echo $e->getMessage();
            }
        }
    }


    // generate css
    foreach ($font_saves as $fs) {
        $css = "@font-face {
    font-family: " . $fs['fontFamily'] . ";
    font-style: " . $fs['fontStyle'] . ";
    font-weight: " . $fs['fontWeight'] . ";
    src: local(''),
        url('assets/addons/fonts/" . $fs['fileName'] . "') format('woff2'),
    }
    ";

        file_put_contents(rex_path::addonAssets("fonts", "fonts.css"), $css, FILE_APPEND);
    }


    echo "<h3>Folgende Schriftarten wurden installiert:</h2>";
    echo "<ul>";
    foreach ($font_saves as $fs) {
        echo "<li>" . $fs['fontFamily'] . " " . $fs['fontStyle'] . " " . $fs['fontWeight'] . "</li>";
    }
    echo "</ul>";
}


// selected font
$selected_font = rex_get("sel_font", "string", "");
if ($selected_font != "") {


    try {
        $socket = rex_socket::factory('google-webfonts-helper.herokuapp.com', '443', true);
        $socket->setPath('/api/fonts/' . $selected_font);

        $response = $socket->doGet();

        if ($response->isOk()) {
            $json = $response->getBody();
            $font = json_decode($json, true);
        } else {
            echo "Error";
            exit;
        }
    } catch (rex_socket_exception $e) {
        echo $e->getMessage();
        exit;
    }

    echo '<div class="panel panel-default">
            <div class="panel-heading">Varianten auswählen</div>
            <div class="panel-body">
            <form method="POST">';

    foreach ($font["variants"] as $fv) {
        echo '<input type="checkbox" name="selected_variants[]" value="' . $fv['id'] . '" /> ' . $fv['fontWeight'] . " " . $fv['fontStyle'] . "<br>";
    }

    echo '<button style="margin-bottom: 10px;" class="btn btn-primary" type="submit" >Ausgewählte Schriftarten installieren</button>';
    echo "</form></div></div>";
}

// get available fonts
try {
    $socket = rex_socket::factory('google-webfonts-helper.herokuapp.com', '443', true);
    $socket->setPath('/api/fonts');

    $response = $socket->doGet();

    if ($response->isOk()) {
        $json = $response->getBody();
        $available_fonts = json_decode($json, true);
    } else {
        echo "Error";
        exit;
    }
} catch (rex_socket_exception $e) {
    echo $e->getMessage();
    exit;
}





$table_rows = "";
foreach ($available_fonts as $f) {
    $table_rows .= "<tr>";
    $table_rows .= "<td>{$f['family']}</td>";
    $table_rows .= '<td><a href="' . rex_url::currentBackendPage(['sel_font' => $f['id']]) . '">Installieren</a></td>';
    $table_rows .= "</tr>";
}

?>



<input style="margin-top: 10px; margin-bottom: 10px;" placeholder="Schrift suchen ..." class="form-control" id="input_filter" type="text" onkeyup="filter()">

<form id="form_fonts" method="POST">
    <table id="fonts_table" class="table table-border table-striped">
        <tr>
            <th>Name</th>
            <th>Auswählen</th>
        </tr>
        <?php echo $table_rows; ?>
    </table>
</form>


<script>
    function filter() {
        // Declare variables
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("input_filter");
        filter = input.value.toUpperCase();
        table = document.getElementById("fonts_table");
        tr = table.getElementsByTagName("tr");

        // Loop through all table rows, and hide those who don't match the search query
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
</script>