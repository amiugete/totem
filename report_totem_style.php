<?php
// INIZIO
?>


<style>

/* PARTE FISSA */
.intestazione-tabella1 {
    flex-shrink: 0;
}

.contenitore-tabella {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
}

.colonna-nascosta {
  width: 0 !important;
  min-width: 0 !important;
  max-width: 0 !important;
  padding: 0 !important;
  border: 0 !important;
  overflow: hidden !important;
}

td.colonna-nascosta,
th.colonna-nascosta {
    display: none !important;
}

.icona-modificata {
    color: #ff0000;
    display: inline-block;
    animation: matita-pulse 1.2s ease-in-out infinite;
}

@keyframes matita-pulse {
    0%, 100% {
        transform: scale(1);
        opacity: 1;
    }

    50% {
        transform: scale(1.35);
        opacity: 0.55;
    }
}


</style>


<?php
// FINE
?>