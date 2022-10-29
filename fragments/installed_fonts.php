<table class="table table-bordered">
    <tr>
        <th>Schriftart</th>
        <th>Gewichte</th>
        <th>Code Schnipsel</th>
    </tr>

    <?php
    foreach ($this->installed_fonts as $font) {
        echo "<tr>";
        echo "<td>" . $font["name"] . "</td>";
        echo "<td>" . $font["weights"] . "</td>";
        echo "<td>";
        echo "<code>";
        echo htmlspecialchars('<link rel="stylesheet" href="' . rex_path::addonAssets("fonts", $font["name"] . "/" . $font["name"] . ".css") . '">');
        echo "</code>";
        echo  "</td>";
        echo  "</tr>";
    }
    ?>

</table>