<?php

@include 'config.php';  // Inkluderer konfigurasjonsfilen, antagelig inneholder databasekoblingsdetaljer eller andre konfigurasjonsdetaljer.

session_start();  // Starter sesjonen. Dette er nødvendig for å få tilgang til eksisterende sesjonsvariabler.

session_unset();  // Frigjør alle sesjonsvariabler. Dette vil slette alle data som er assosiert med den gjeldende sesjonen.

session_destroy();  // Ødelegger hele sesjonen. Dette fjerner sesjonen, og brukeren vil ikke lenger være logget inn.

header('location:login_form.php');  // Omdirigerer brukeren tilbake til innloggingsskjemaet.

?>
