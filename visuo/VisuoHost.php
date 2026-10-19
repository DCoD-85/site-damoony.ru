<?PHP
// visuo/VisuoHost.php

interface VisuoHost {
    public function getRootTag(): visuotag;
    public function applyPageOverrides(visuostruct_html $struct): void;
}