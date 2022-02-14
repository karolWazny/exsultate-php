<?php

class ExsultateSongDisplayFormatter
{
    private $post_id;
    private $content;

    private $song;
    private $restricted;

    private $song_block;

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
        $this->post_id = $post_id;
        $this->content = $raw_content;

        $this->do_magic();



        //it is super important we assign it here,
        //not at any place before;
        //so if any exception occurs, our data stays in its initial state
        //so it is kind of safe
        $raw_content = self::$_prefix . serialize_block($this->song_block) . self::$_the_thing_in_between . self::$_suffix;
        return $raw_content;
    }

    private function do_magic(){
        $object = json_decode($this->content, true);
        $this->song = $object['song'];
        $this->restricted = $object['restricted'];

        $this->build_song_block();
    }

    private function build_song_block(){
        $output = [
            'blockName' => 'lazyblock/song',
            'attrs'=> [
                'music-author'=>$this->song['music'],
                'lyrics-author'=>$this->song['lyrics'],
                'translation-author'=>$this->song['translated']
            ],
            'innerBlocks'=>$this->build_song_inner_blocks(),
            //todo get to know WTF is going on with these two:
            'innerContent'=>["\n",null,"\n\n",null,"\n"],
            'innerHTML'=>"\n\n\n\n"
        ];

        $myfile = fopen(plugin_dir_path( __FILE__ ) . "built_song.txt", "w");
        fwrite($myfile,json_encode($output));
        fclose($myfile);

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