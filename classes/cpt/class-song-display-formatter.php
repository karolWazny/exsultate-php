<?php

class ExsultateSongDisplayFormatter
{
    public function format( &$raw_content, $post_id) {
        $raw_content = $raw_content . 'some test content';
        return $raw_content;
    }
}