<?php


function get_folders(string $path): array
{
    $files_and_folders = scandir($path);

    // remove . and .. symlinks
    $files_and_folders = array_diff($files_and_folders, [".", ".."]);

    $folders = [];

    // remove files
    foreach ($files_and_folders as $faf) {
        if (is_dir(rex_path::addonAssets("fonts", $faf))) {
            $folders[] = $faf;
        }
    }

    return $folders;
}

function get_files(string $path): array
{
    $files_and_folders = scandir($path);

    // remove . and .. symlinks
    $files_and_folders = array_diff($files_and_folders, [".", ".."]);

    $files = [];

    // remove folders
    foreach ($files_and_folders as $faf) {
        if (!is_dir(rex_path::addonAssets("fonts", $faf))) {
            $files[] = $faf;
        }
    }

    return $files;
}


$font_folders = get_folders(rex_path::addonAssets("fonts"));

$fonts = [];

foreach ($font_folders as $folder) {
    $files = get_files(rex_path::addonAssets("fonts", $folder));

    // font weights
    $re = '/[0-9]{3}\.woff2/';
    $weights = [];

    foreach ($files as $file) {
        if (str_ends_with($file, ".woff2")) {
            preg_match($re, $file, $matches);
            $weights[] = str_replace(".woff2", "", $matches[0]);
        }
    }

    $weights = implode(",", $weights);

    $fonts[] = [
        "name" => $folder,
        "weights" => $weights,
    ];
}

$fragment = new rex_fragment();
$fragment->setVar("installed_fonts", $fonts, false);
echo $fragment->parse("installed_fonts.php");
