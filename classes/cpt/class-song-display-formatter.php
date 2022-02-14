<?php

class ExsultateSongDisplayFormatter
{
    private $post_id;
    private $content;

    private static $_prefix = <<<END
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"66.66%"} -->
<div class="wp-block-column" style="flex-basis:66.66%">
END;

    private static $_the_thing_in_between = <<<END
<!-- /wp:column -->

<!-- wp:column {"width":"33.33%"} -->
<div class="wp-block-column" style="flex-basis:33.33%">
END;

    private static $_suffix = <<<END
</div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
END;

    public function format( &$raw_content, $post_id) {
        $this->post_id = $post_id;
        $this->content = $raw_content;

        $this->do_magic();

        $myfile = fopen(plugin_dir_path( __FILE__ ) . "to_be_displayed.txt", "w");
        fwrite($myfile,$this->content);
        fclose($myfile);

        $raw_content = $this->content;
        return $raw_content;
    }

    private function do_magic(){

    }
}