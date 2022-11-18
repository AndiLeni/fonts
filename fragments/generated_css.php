<h3><?php echo rex_i18n::msg("fonts_installed") ?></h3>
<ul>
    <?php
    foreach ($this->font_saves as $fs) {
        foreach ($fs as $f) {
            echo "<li>" . $f['fontFamily'] . " " . $f['fontStyle'] . " " . $f['fontWeight'] . "</li>";
        }
    }
    ?>
</ul>

<div class="well">
    <strong><?php echo rex_i18n::msg("fonts_installed_available_all") ?></strong>
    <p>
        <a href="<?php echo rex_url::addonAssets('fonts', 'gfonts.css') ?>" target="_blank">
            <?php echo rex_url::addonAssets('fonts', 'gfonts.css') ?>
        </a>
    </p>
    <p>
        <code>
            <?php echo htmlspecialchars('<link href="<?= rex_url::addonAssets("fonts", "gfonts.css"); ?>" rel="stylesheet">') ?>
        </code>
    </p>
    <strong><?php echo rex_i18n::msg("fonts_installed_available_single") ?></strong>
    <p>
        <a href="<?php echo rex_url::addonAssets('fonts', array_key_first($this->font_saves) . '/' . array_key_first($this->font_saves) . '.css') ?>" target="_blank">
            <?php echo rex_url::addonAssets('fonts', array_key_first($this->font_saves) . '/' . array_key_first($this->font_saves) . '.css') ?>
        </a>
    </p>
    <p>
        <code>

            <?php
            echo htmlspecialchars('<link href="<?= rex_url::addonAssets("fonts", "' . array_key_first($this->font_saves) . '/' . array_key_first($this->font_saves) . '.css") ?>" rel="stylesheet">');
            ?>
        </code>
    </p>
</div>