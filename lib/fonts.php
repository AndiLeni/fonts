<?php

class fonts
{
    public static function getLicense(String $fontName): string|false
    {
        if ($fontName != "") {
            $fontName = str_replace(' ', '', strtolower($fontName));
            $possible_folders = ['apache', 'ofl', 'ufl'];
            $license = '';
            foreach ($possible_folders as $folder) {
                switch ($folder) {
                    case 'apache':
                        $fileName = 'LICENSE.txt';
                        break;
                    case 'ofl':
                        $fileName = 'OFL.txt';
                        break;
                    case 'ufl':
                        $fileName = 'UFL.txt';
                }

                try {
                    $socket = rex_socket::factory('raw.githubusercontent.com', 443, true);
                    $socket->setPath('/google/fonts/main/' . $folder . '/' . $fontName . '/' . $fileName);
                    $socket->addHeader('User-Agent', 'REDAXO Fonts-AddOn');
                    $response = $socket->doGet();
                    if ($response->isOk()) {
                        $content = $response->getBody();
                        $license .= $content;
                    }
                } catch (rex_socket_exception $e) {
                    echo rex_view::error($e->getMessage());
                }
            } // End foreach
            return $license;
        }
        return false;
    } // End getLicence()

    public static function generateCss(array $font_saves): void
    {
        foreach ($font_saves as $key => $fs) {
            $css = '';
            foreach ($fs as $f) {
                $fragment = new rex_fragment();
                $fragment->setVar('key', $key, false);
                $fragment->setVar('fontFamily', $f['fontFamily'], false);
                $fragment->setVar('fontStyle', $f['fontStyle'], false);
                $fragment->setVar('fontWeight', $f['fontWeight'], false);
                $fragment->setVar('identifier', $f['identifier'], false);
                $fragment->setVar('formats', $f['formats'], false);
                $css .= $fragment->parse('css_fontface.php');
            }
            // CSS generieren
            file_put_contents(rex_path::addonAssets("fonts", $key . '/' . $key . ".css"), $css, FILE_APPEND);
            $css_files[] = $key . '/' . $key . ".css";

            // Lizenz holen und ebenfalls speichern
            file_put_contents(rex_path::addonAssets("fonts", $key . '/LICENSE.txt'), fonts::getLicense(strtolower($f['fontFamily'])));

            // Todo: Installierte Fonts in Rex-Config speichern
            // In Rex-Config speichern
            /*
            $installed_fonts = rex_config::get('fonts', 'installed_fonts' );
            $installed_fonts = rex_var::toArray($installed_fonts);
            $installed_fonts[] = $key;
            $installed_fonts = json_encode($installed_fonts);
            rex_config::set('fonts', 'installed_fonts', $installed_fonts );
            */
        }

        // remove leading '..' from url for correct ressource loading in the browser
        $addon_assets_path = ltrim(rex_url::addonAssets('fonts'), '.');

        foreach ($css_files as $css_file) {
            $css_all = '@import url("' . $addon_assets_path . $css_file . '");' . PHP_EOL;
            file_put_contents(rex_path::addonAssets("fonts/", "gfonts.css"), $css_all, FILE_APPEND);
        }
    } // End generateCss()

}
