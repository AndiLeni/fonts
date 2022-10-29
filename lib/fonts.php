<?php

class fonts
{
    public static function getLicense($fontName)
    {
        if ($fontName) {
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
                $url = 'https://raw.githubusercontent.com/google/fonts/main/' . $folder . '/' . $fontName . '/' . $fileName;
                #dump($url);
                $curly = curl_init();
                curl_setopt($curly, CURLOPT_URL, $url);
                curl_setopt($curly, CURLOPT_HEADER, 0);
                curl_setopt($curly, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curly, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($curly, CURLOPT_USERAGENT, "REDAXO Fonts-AddOn");
                $content = curl_exec($curly);
                curl_close($curly);
                if ($content !== '404: Not Found') {
                    $license .= $content;
                }
            } // End foreach
            return $license;
        }
        return false;
    } // End getLicence()

    public static function generateCss($font_saves)
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

        foreach ($css_files as $css_file) {
            $css_all = '@import url("' . rex_url::addonAssets('fonts') . $css_file . '");' . PHP_EOL;
            file_put_contents(rex_path::addonAssets("fonts/", "gfonts.css"), $css_all, FILE_APPEND);
        }
    } // End generateCss()

}
