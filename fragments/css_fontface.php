@font-face {
    /* <?php echo $this->key ?> */
    font-family: '<?php echo $this->fontFamily ?>';
    font-style: <?php echo $this->fontStyle ?>;
    font-weight: <?php echo $this->fontWeight ?>;
    font-display: swap;
    src: url('assets/addons/fonts/<?php echo $this->identifier ?>.eot'); /* IE9 Compat Modes */
    src: local(''),
    <?php
    foreach ($this->formats as $fkey => $fformat) {
        echo "      url('assets/addons/fonts/" . $fformat . "') format('$fkey')," . PHP_EOL;
    }
    ?>
}
