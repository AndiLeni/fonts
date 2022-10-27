<div class="panel panel-default">
    <div class="panel-heading">
        Varianten auswählen für <strong><?php echo $this->getVar("selected_font") ?></strong>
    </div>
    <div class="panel-body">

        <div id="previewFontSizeInput" class="input-group col-md-3" style="margin-bottom: 25px;">
            <span class="input-group-addon">Vorschau-Schriftgöße</span>
            <input class="form-control input-sm" type="number" value="16" placeholder="16" id="fonts-preview-fontsize"> <span class="input-group-addon" id="px-addon">px</span>
        </div>

        <form id="select_variants_form" method="POST">
            <?php echo $this->getVar("variant_options") ?>
        </form>

        <h3>Lizenz</h3>
        <div class="row">
            <div class="col-md-6">
                 <textarea class="form-control" style="height: 250px;" readonly>
            <?= fonts::getLicense( $this->getVar("selected_font")) ?>
        </textarea>
            </div>
        </div>

    </div>
    <div class="panel-footer">
        <button form="select_variants_form" class="btn btn-primary" type="submit">Ausgewählte Schriftarten installieren</button>
    </div>
</div>

<script>
    $(document).on('rex:ready', function() {
        $('#fonts-preview-fontsize').on('change keyup',function (e) {
            console.log( $(this).val() );
            var fontSize = $(this).val();
            if(!fontSize) {
                fontSize = 16;
            }
            $('#select_variants_form span').each(function () {
                $(this).css('font-size', fontSize + 'px');
            })
        })
    });
</script>