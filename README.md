# REDAXO Fonts Addon
Dieses Addon kann Schriftarten von Google Fonts auf den Webspace laden um diese datenschutzkonform zu nutzen.
Genutzt wird dafür die API von gwfh.mranftl.com. Schriftdateien werden direkt von Google heruntergeladen.

![splashscreen](https://user-images.githubusercontent.com/16903055/198571774-f7a79435-3925-41f1-9ef3-797ce97041d2.png)

## How-To
1. Benötigte Schriftart auf der Addon-Seite auswählen und installieren
2. Die Schriftarten werden in das Assets-Verzeichnis des Fonts-AddOns heruntergeladen: `/assets/addons/fonts/name-der-schriftart`
3. Man kann entweder alle heruntergeladenen Schriften per CSS einbinden - hierfür liegt im Assets-Ordner die Datei `/assets/addons/fonts/gfonts.css`, welche alle anderen Schrift-Styles included oder man kann die einzelnen Schriften einbinden. Hierzu enthält jeder Schriftarten-Ordner eine CSS-Datei der jeweiligen Schriftart. Z.B.: `/assets/fonts/addons/poppins-v20-latin/poppins-v20-latin.css`

### Alle einbinden
`<link href="<?= rex_url::addonAssets('fonts','gfonts.css') ?>" rel="stylesheet">`
### Nur eine bestimmte Schrift einbinden
Hier bitte im Asset-Ordner nachsehen, wie der Name der Font/der Ordner der Schriftart lautet.

`<link href="<?= rex_url::addonAssets('fonts','name-der-schriftart/name-der-schriftart.css') ?>" rel="stylesheet">`