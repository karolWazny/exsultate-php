<?php

class ExsultateSongSaveFormatter
{
    private $post_content;
    private $song_block;
    private $restrictor_block;
    private $blocks;

    public function format( &$data ) {
        try{
            $this->post_content = $data['post_content'];

            $this->unquote_content();

            $this->parse_blocks();
            $this->find_song_block();
            $this->find_access_restrictor_block();

            $this->quote_content();

            //it is super important we assign it here,
            //not at any place before;
            //so if any exception occurs, our data stays in its initial state
            //so it is kind of safe
            $data['post_content'] = $this->post_content;
        } catch (Exception $e) {
        }

        return $data;
    }

    private function parse_blocks(){
        $this->blocks = parse_blocks($this->post_content);
    }

    private function find_block_named( $blockName ){
        return $this->find_block_recursive( $blockName, $this->blocks);
    }

    private function find_block_recursive ($blockName, $blocks) {
        foreach ($blocks as $block) {
            if($block['blockName'] === $blockName)
                return $block;
            $output = $this->find_block_recursive($blockName, $block['innerBlocks']);
            if (! is_null($output))
                return $output;
        }
        return null;
    }

    private function find_access_restrictor_block(){
        $this->restrictor_block = $this->find_block_named('lazyblock/access-restriction');
    }

    /**
     * @throws ErrorException
     */
    private function find_song_block(){
        $this->song_block = $this->find_block_named('lazyblock/song');

        if(is_null($this->song_block))
            throw new ErrorException('Song post does not contain song block!');
    }

    private function unquote_content(){
        $this->post_content = stripslashes($this->post_content);
    }

    private function quote_content(){
        $this->post_content = addslashes($this->post_content);
    }
}