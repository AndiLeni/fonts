@font-face {
    font-family: <?php echo $this->getVar("fontFamily") ?>;
    font-style: <?php echo $this->getVar("fontStyle") ?>;
    font-weight: <?php echo $this->getVar("fontWeight") ?>;
    src: local(''),
        url('assets/addons/fonts/<?php echo $this->getVar("fileName") ?>.woff2') format('woff2'),
}
