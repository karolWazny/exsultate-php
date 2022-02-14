<?php

class ExsultateSongSaveFormatter
{
    private $post_content;
    private $blocks;

    private $song_block;

    private $song = [];

    private $restrictor_block;
    private $restricted_content;

    public function format( &$data ) {
        $this->song = [];
        try{
            $this->post_content = $data['post_content'];
            $this->song['title'] = trim($data['post_title']);

            $this->unquote_content();

            $this->parse_blocks();
            $this->find_song_block();
            $this->find_access_restrictor_block();

            $this->obtain_restricted_content();
            $this->build_song_object();

            $final_content = [
                "song"=>$this->song,
                "restricted"=>$this->restricted_content
                ];

            $this->post_content = json_encode($final_content);

            $this->quote_content();

            //it is super important we assign it here,
            //not at any place before;
            //so if any exception occurs, our data stays in its initial state
            //so it is kind of safe
            //$data['post_content'] = $this->post_content;
        } catch (Exception $e) {
        }

        return $data;
    }

    private function build_song_object(){
        $this->song['lyrics'] = $this->song_block['attrs']['lyrics-author'];
        $this->song['music'] = $this->song_block['attrs']['music-author'];
        $this->song['translated'] = $this->song_block['attrs']['translation-author'];
        $this->song['content'] = $this->obtain_song_parts();
    }

    private function obtain_song_parts(){
        $song_content = [];
        foreach ($this->song_block['innerBlocks'] as $inner_block){
            $type = null;
            switch ($inner_block['blockName']){
                case 'lazyblock/verse':
                    $type = 'verse';
                    break;
                case 'lazyblock/chorus':
                    $type = 'chorus';
                    break;
                default:
                    break;
            }
            if(is_null($type))
                continue;
            $song_part = [];
            $song_part['type'] = $type;
            $song_part['content'] = $inner_block['attrs']['content'];
            $song_content[] = $song_part;
        }
        return $song_content;
    }

    private function obtain_restricted_content(){
        $this->restricted_content = '';
        if(is_null($this->restrictor_block)){
            return;
        }
        $restricted_blocks = $this->restrictor_block['innerBlocks'];
        foreach ($restricted_blocks as $restricted_block) {
            $this->restricted_content = $this->restricted_content . serialize_block($restricted_block);
        }
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