function copyStringToClipboard (str) {
    let clipboard = navigator.clipboard;

    if (clipboard !== undefined ){
        //this is the preferred way
        navigator.clipboard.writeText('dupa');
        console.log('Copied: ' + str);
    } else {
        //but sometimes, e.g. when site source is considered unsafe,
        //clipboard is not accessible from js and this is the only way:
        // Create new element
        var el = document.createElement('textarea');
        // Set value (string to be copied)
        el.value = str;
        // Set non-editable to avoid focus and move outside of view
        el.setAttribute('readonly', '');
        el.style = {position: 'absolute', left: '-9999px'};
        document.body.appendChild(el);
        // Select text inside element
        el.select();
        // Copy text to clipboard
        document.execCommand('copy');
        // Remove temporary element
        document.body.removeChild(el);
    }
}

function get_song_parts(song) {
    let entire_song = document.getElementById("song_with_chords");
    let song_contents = entire_song.querySelectorAll("*");
    let song_parts = new Array();
    Array.from(song_contents).forEach(div => {
        if(div.className.normalize() === "exsultate_verse" || div.className === "exsultate_chorus") {
            song_parts.push(div);
        }
    });
    return song_parts;
}

function get_lyrics() {
    let song_parts = get_song_parts();
    let lyrics = "";
    song_parts.forEach(div => {
        let song_section = "";
        let cpress_lines = div.querySelectorAll(".cpress_line");
        Array.from(cpress_lines).forEach(line => {
            let line_text = "";
            let line_sections = line.querySelectorAll(".lyric");
            Array.from(line_sections).forEach(sec => {
                line_text = line_text.concat(sec.innerText);
            });
            if(line_text === "") {
                line_text = line.innerText;
            }
            line_text = line_text.trim().replace(/\s+/g,' ');
            song_section = song_section.concat(line_text).concat('\n');
        });
        lyrics = lyrics.concat(song_section);
    });

    copyStringToClipboard(lyrics.trim());
}

function get_lyrics_and_chords(){
    let song_parts = get_song_parts();
    let lyrics = "";
    song_parts.forEach(div => {
        let song_section = "";
        let cpress_lines = div.querySelectorAll(".cpress_line");
        Array.from(cpress_lines).forEach(line => {
            let line_text = "";
            let line_chords = "|";
            let line_sections = line.querySelectorAll(".lyric");
            Array.from(line_sections).forEach(sec => {
                line_text = line_text.concat(sec.innerText);
            });
            if(line_text === "") {
                line_text = line.innerText;
            }

            let chord_sections = line.querySelectorAll(".chord");
            Array.from(chord_sections).forEach(sec => {
                if(sec.style.display != "none") {
                    line_chords = line_chords.concat(" ").concat(sec.innerText.trim());
                }
            });

            line_text = line_text.trim().concat(" ").concat(line_chords).replace(/\s+/g,' ');
            if(line_text.trim() != "|") {
                song_section = song_section.concat(line_text).concat('\n');
            }
        });
        lyrics = lyrics.concat(song_section).concat('\n');
    });

    copyStringToClipboard(lyrics.trim());
}