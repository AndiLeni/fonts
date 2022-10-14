<?php

echo rex_view::title('Google Fonts für REDAXO');

// parameter check
$selected_variants = rex_post("selected_variants", "array", []);
$selected_font = rex_get("sel_font", "string", "");


// selected variants to download
if ($selected_variants != []) {

    try {
        $socket = rex_socket::factory('google-webfonts-helper.herokuapp.com', '443', true);
        $socket->setPath('/api/fonts/' . $selected_font);

        $response = $socket->doGet();

        if ($response->isOk()) {
            $json = $response->getBody();
            $font = json_decode($json, true);

            // save downloaded fonts for css generation
            $font_saves = [];

            // download font files
            foreach ($font['variants'] as $fv) {
                if (in_array($fv['id'], $selected_variants)) {

                    $font_name = str_replace("'", "", $fv['fontFamily']);

                    // woff2 file
                    $filename = $font_name . '-' . $fv['id'] . ".woff2";
                    $url = parse_url($fv['woff2']);

                    try {
                        $socket = rex_socket::factory($url['host'], '443', true);
                        $socket->setPath($url['path']);

                        $response = $socket->doGet();

                        if ($response->isOk()) {
                            $body = $response->getBody();

                            $saved = file_put_contents(rex_path::addonAssets("fonts", $filename), $body);
                            if ($saved != false) {
                                $font_saves[$font_name] = [
                                    "fontFamily" => $fv['fontFamily'],
                                    "fontStyle" => $fv['fontStyle'],
                                    "fontWeight" => $fv['fontWeight'],
                                    "fileName" => $filename
                                ];
                            } else {
                                echo rex_view::error("Schriftart konnte nicht gespeichert werden.");
                            }
                        } else {
                            echo rex_view::error("Download der Schrift von Google nicht möglich. Response Code != 200 OK");
                        }
                    } catch (rex_socket_exception $e) {
                        echo rex_view::error($e->getMessage());
                    }
                }
            }

            // generate css
            foreach ($font_saves as $fs) {

                $fragment = new rex_fragment();
                $fragment->setVar('fontFamily', $fs['fontFamily'], false);
                $fragment->setVar('fontStyle', $fs['fontStyle'], false);
                $fragment->setVar('fontWeight', $fs['fontWeight'], false);
                $fragment->setVar('fileName', $fs['fileName'], false);
                $css = $fragment->parse('css_fontface.php');

                $saved = file_put_contents(rex_path::addonAssets("fonts", "fonts.css"), $css, FILE_APPEND);
                if ($saved == false) {
                    echo rex_view::error("CSS Datei konnte nicht gespeichert werden.");
                }
            }


            echo "<h3>Folgende Schriftarten wurden installiert:</h2>";
            echo "<ul>";
            foreach ($font_saves as $fs) {
                echo "<li>" . $fs['fontFamily'] . " " . $fs['fontStyle'] . " " . $fs['fontWeight'] . "</li>";
            }
            echo "</ul>";
        } else {
            echo rex_view::error("API Zugriff auf google-webfonts-helper.herokuapp.com nicht möglich. Response Code != 200 OK");
        }
    } catch (rex_socket_exception $e) {
        echo rex_view::error($e->getMessage());
    }
}


// select variants
if ($selected_variants == [] && $selected_font != "") {
    try {
        $socket = rex_socket::factory('google-webfonts-helper.herokuapp.com', '443', true);
        $socket->setPath('/api/fonts/' . $selected_font);

        $response = $socket->doGet();

        if ($response->isOk()) {
            $json = $response->getBody();
            $font = json_decode($json, true);

            $variant_options = "";
            foreach ($font["variants"] as $fv) {
                $variant_options .= '<input type="checkbox" name="selected_variants[]" value="' . $fv['id'] . '" /> ' . $fv['fontWeight'] . " " . $fv['fontStyle'] . "<br>";
            }

            $fragment = new rex_fragment();
            $fragment->setVar('variant_options', $variant_options, false);
            $fragment->setVar('selected_font', $font["family"], true);
            echo $fragment->parse('select_variants_panel.php');
        } else {
            echo rex_view::error("API Zugriff auf google-webfonts-helper.herokuapp.com nicht möglich. Response Code != 200 OK");
        }
    } catch (rex_socket_exception $e) {
        echo rex_view::error($e->getMessage());
    }
}


// if no font is selected, list all available fonts
if ($selected_font == "") {
    try {
        $socket = rex_socket::factory('google-webfonts-helper.herokuapp.com', '443', true);
        $socket->setPath('/api/fonts');

        $response = $socket->doGet();

        if ($response->isOk()) {
            $json = $response->getBody();
            $available_fonts = json_decode($json, true);

            $table_rows = "";
            foreach ($available_fonts as $f) {
                $table_rows .= "<tr>";
                $table_rows .= "<td>{$f['family']}</td>";
                $table_rows .= '<td><a href="' . rex_url::currentBackendPage(['sel_font' => $f['id']]) . '">Installieren</a></td>';
                $table_rows .= "</tr>";
            }

            $fragment = new rex_fragment();
            $fragment->setVar('table_rows', $table_rows, false);
            echo $fragment->parse('all_available_fonts_table.php');
        } else {
            echo rex_view::error("API Zugriff auf google-webfonts-helper.herokuapp.com nicht möglich. Response Code != 200 OK");
        }
    } catch (rex_socket_exception $e) {
        echo rex_view::error($e->getMessage());
    }
}
