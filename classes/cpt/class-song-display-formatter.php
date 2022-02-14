<?php

class ExsultateSongDisplayFormatter
{
    private $post_id;
    private $content;

    public function format( &$raw_content, $post_id) {
        $this->post_id = $post_id;
        $this->content = $raw_content;

        $this->do_magic();

        $raw_content = $this->content;
        return $raw_content;
    }

    private function do_magic(){

    }
}