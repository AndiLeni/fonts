<?php

// parameter check
$selected_variants = rex_post("selected_variants", "array", []);
$selected_font = rex_get("sel_font", "string", "");

#echo '$formats';
$formats = ['eot', 'woff', 'woff2', 'ttf', 'svg'];
#dump($formats);
#dump('----');

// selected variants to download
if ($selected_variants != []) {
    try {
        $socket = rex_socket::factory('google-webfonts-helper.herokuapp.com', 443, true);
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
        echo rex_view::error($e->getMessage());
        exit;
    }

    $font_saves = [];
    #echo '$font';
    #dump($font);

    foreach ($font['variants'] as $fkey => $fv) {
        $available_font_formats = [];
        $dirName = $font['id'] . '-' . $font['version'] . '-' . $font['defSubset'];

        if (!is_dir(rex_path::addonAssets('fonts', $dirName))) {
            mkdir(rex_path::addonAssets('fonts', $dirName));
        }

        if (in_array($fv['id'], $selected_variants, true)) {

            foreach ($formats as $format) {
                if (array_key_exists($format, $fv)) {
                    #$filename = str_replace("'", "", $fv['fontFamily']) . '-' . $fv['id'] . '.'.$format;
                    #$filename = id version subset weight
                    $filename = $font['id'] . '-' . $font['version'] . '-' . $font['defSubset'] . '-' . $fv['fontStyle'] . '-' . $fv['fontWeight'] . '.' . $format;
                    $url = parse_url($fv[$format]);

                    #echo '$fv:';
                    #dump($fv);
                    #echo '$fv[format]:';
                    #dump($fv[$format]);
                    #echo '$format:';
                    #dump($format);

                    try {
                        $socket = rex_socket::factory($url['host'], 443, true);
                        $socket->setPath($url['path']);

                        $response = $socket->doGet();

                        if ($response->isOk() || $format == 'svg') {

                            $font_family = str_replace("'", "", $fv['fontFamily']);

                            if ($format == 'svg') {
                                $body = file_get_contents($fv['svg']);
                                $available_font_formats[$format] = $filename . '#' . str_replace(" ", "", $font_family);
                            } else {
                                $body = $response->getBody();
                                $available_font_formats[$format] = $filename;
                            }
                            #dump($body);
                            rex_file::put(rex_path::addonAssets("fonts", $dirName . '/' . $filename), $body);

                            $font_saves[$font['id'] . '-' . $font['version'] . '-' . $font['defSubset']][$fv['fontWeight'] . '-' . $fv['fontStyle']] = [
                                "fontFamily" => $font_family,
                                "fontStyle" => $fv['fontStyle'],
                                "fontWeight" => $fv['fontWeight'],
                                "fileName" => $filename,
                                "name" => $font['id'] . '-' . $font['version'] . '-' . $fv['fontWeight'] . '-' . $fv['fontStyle'],
                                "formats" => $available_font_formats,
                                "id" => $font['id'],
                                "version" => $font['version'],
                                "subset" => $font['defSubset'],
                                "identifier" => $font['id'] . '-' . $font['version'] . '-' . $font['defSubset']
                            ];
                        } else {
                            echo "Error " . $filename;
                        }
                    } catch (rex_socket_exception $e) {
                        echo rex_view::error($e->getMessage());
                    }
                }
            }
        }
    }

    #echo '$font_saves';
    #dump($font_saves);

    // generate css
    fonts::generateCss($font_saves);

    // show <link> for inclusion into template
    $fragment = new rex_fragment();
    $fragment->setVar("font_saves", $font_saves, false);
    echo $fragment->parse("generated_css.php");
}


// select variants
if ($selected_variants == [] && $selected_font != "") {
    try {
        $socket = rex_socket::factory('google-webfonts-helper.herokuapp.com', 443, true);
        $socket->setPath('/api/fonts/' . $selected_font);

        $response = $socket->doGet();

        if ($response->isOk()) {
            $json = $response->getBody();
            $font = json_decode($json, true);

            $variant_options = "";
            foreach ($font["variants"] as $fv) {
                $previewCss =  '<link href="https://fonts.googleapis.com/css?family=' . str_replace(" ", "+", $fv['fontFamily']) . ':100,200,300,400,500,600,700,800,900&display=swap" rel="stylesheet">';
                echo str_replace("'", '', $previewCss);
                $variant_options .= '
                <label for="' . $fv['id'] . '">
                    <input id="' . $fv['id'] . '" type="checkbox" name="selected_variants[]" value="' . $fv['id'] . '" /> 
                        ' . $fv['fontWeight'] . " " . $fv['fontStyle'] . " <span style=\"font-size: 16px; padding-left: 100px; font-family: " . $fv['fontFamily'] . "; font-weight: " . $fv['fontWeight'] . "; font-style: " . $fv['fontStyle'] . " \">" . str_replace("'", "", $fv['fontFamily']) . " - " . $fv['fontWeight'] . " " . $fv['fontStyle'] . "</span>
                </label>
                <br>";
            }

            $fragment = new rex_fragment();
            $fragment->setVar('variant_options', $variant_options, false);
            $fragment->setVar('selected_font', $font["family"], true);
            echo $fragment->parse('select_variants_panel.php');
        } else {
            echo rex_view::error(rex_i18n::msg("fonts_error_api_response_code"));
        }
    } catch (rex_socket_exception $e) {
        echo rex_view::error($e->getMessage());
    }
}

// get available fonts
try {
    $socket = rex_socket::factory('google-webfonts-helper.herokuapp.com', 443, true);
    $socket->setPath('/api/fonts');

    $response = $socket->doGet();

    if ($response->isOk()) {
        $json = $response->getBody();
        $available_fonts = json_decode($json, true);
    } else {
        echo rex_view::error(rex_i18n::msg("fonts_error_api_response_code"));
        exit;
    }
} catch (rex_socket_exception $e) {
    echo rex_view::error($e->getMessage());
    exit;
}

#dump($available_fonts);


// if no font is selected, list all available fonts
if ($selected_font == "") {
    try {
        $socket = rex_socket::factory('google-webfonts-helper.herokuapp.com', 443, true);
        $socket->setPath('/api/fonts');

        $response = $socket->doGet();

        if ($response->isOk()) {
            $json = $response->getBody();
            $available_fonts = json_decode($json, true);

            $table_rows = "";
            foreach ($available_fonts as $f) {
                $table_rows .= "<tr>";
                $table_rows .= "<td>{$f['family']}</td>";
                $table_rows .= '<td><a href="' . rex_url::currentBackendPage(['sel_font' => $f['id']]) . '">' . rex_i18n::msg("fonts_install") . '</a></td>';
                $table_rows .= "</tr>";
            }

            $fragment = new rex_fragment();
            $fragment->setVar('table_rows', $table_rows, false);
            echo $fragment->parse('all_available_fonts_table.php');
        } else {
            echo rex_view::error(rex_i18n::msg("fonts_error_api_response_code"));
        }
    } catch (rex_socket_exception $e) {
        echo rex_view::error($e->getMessage());
    }
}
