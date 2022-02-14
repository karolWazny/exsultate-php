<?php

class ExsultateSongDisplayFormatter
{
    private $post_id;
    private $content;

    private $song;
    private $restricted;

    private $song_block;
    private $restricted_block;

    private static $_prefix = <<<END
<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"66.66%"} -->
<div class="wp-block-column" style="flex-basis:66.66%">
END;

    private static $_the_thing_in_between = <<<END
</div>
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

        try{
            $this->post_id = $post_id;
            $this->content = $raw_content;

            $this->extract_objects();
            $this->build_song_block();
            $this->build_restricted_block();

            //it is super important we assign it here,
            //not at any place before;
            //so if any exception occurs, our data stays in its initial state
            //so it is kind of safe
            $raw_content = $this->build_content();
        } catch (Exception $e) {

        }

        return $raw_content;
    }

    private function build_content(){
        return self::$_prefix . serialize_block($this->song_block) . self::$_the_thing_in_between
            . serialize_block($this->restricted_block) . self::$_suffix;
    }

    private function build_restricted_block(){
        $output = [
            'blockName' => 'lazyblock/access-restriction',
            'attrs'=> [

            ],
            'innerBlocks'=>$this->restricted
        ];

        $output['innerContent'] = array_fill(0, count($output['innerBlocks']), null);

        $this->restricted_block = $output;
    }

    private function extract_objects(){
        $object = json_decode($this->content, true);
        $this->song = $object['song'];
        $this->restricted = $object['restricted'];
    }

    private function build_song_block(){
        $output = [
            'blockName' => 'lazyblock/song',
            'attrs'=> [
                'music-author'=>$this->song['music'],
                'lyrics-author'=>$this->song['lyrics'],
                'translation-author'=>$this->song['translated']
            ],
            'innerBlocks'=>$this->build_song_inner_blocks()
        ];

        $output['innerContent'] = array_fill(0, count($output['innerBlocks']), null);

        $this->song_block = $output;
    }

    private function build_song_inner_blocks(){
        $output = [];

        foreach ($this->song['content'] as $song_part){
            $song_part_block = [
                'blockName' => 'lazyblock/' . $song_part['type'],
                'attrs'=> [
                    'content'=>$song_part['content']
                ],
                'innerBlocks'=>[],
                'innerHTML'=>'',
                'innerContent'=>[]
            ];
            $output[] = $song_part_block;
        }

        return $output;
    }
}