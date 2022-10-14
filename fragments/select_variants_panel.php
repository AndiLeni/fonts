<div class="panel panel-default">
    <div class="panel-heading">Varianten auswählen für <b><?php echo $this->getVar("selected_font") ?></b></div>
    <div class="panel-body">
        <form id="select_variants_form" method="POST">
            <?php echo $this->getVar("variant_options") ?>
        </form>
    </div>
    <div class="panel-footer">
        <button form="select_variants_form" class="btn btn-primary" type="submit">Ausgewählte Schriftarten installieren</button>
    </div>
</div>