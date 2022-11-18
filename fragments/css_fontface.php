@font-face {
    /* <?php echo $this->key ?> */
    font-family: '<?php echo $this->fontFamily ?>';
    font-style: <?php echo $this->fontStyle ?>;
    font-weight: <?php echo $this->fontWeight ?>;
    font-display: swap;
    src: url('<?php echo $this->formats['eot'] ?>'); /* IE9 Compat Modes */
    src: local(''),
    <?php
    foreach ($this->formats as $fkey => $fformat) {
        if ($fkey != array_key_last($this->formats)) {
            echo "      url('" . $fformat . "') format('$fkey')," . PHP_EOL;
        } else {
            echo "      url('" . $fformat . "') format('$fkey');" . PHP_EOL;
        }
    }
    ?>
}
