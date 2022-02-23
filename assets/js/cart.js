function set_song_ids(song_ids){
    storage_object().setItem("flyer_items", JSON.stringify(song_ids));
}

function get_song_ids(){
    let flyer_items = JSON.parse(storage_object().getItem("flyer_items"));
    if(!flyer_items){
        flyer_items = [];
        set_song_ids(flyer_items);
    }
    return flyer_items;
}

function add_to_flyer(){
    let flyer_items = get_song_ids();
    let song_id = get_post_id();
    let list_contains = flyer_items.includes(song_id);
    if(!list_contains){
        flyer_items.push(song_id);
        set_song_ids(flyer_items);
    }
    //disable_add_to_cart_button();
}

function get_post_id(){
    let string_value =  document.getElementsByTagName("article")[0].id;
    return parseInt(string_value.split("-")[1]);
}

function clear_cart(){
    set_song_ids([]);
    initialize_songs_list();
}

function initialize_songs_list(){
    let songs = document.getElementById("exsultate_songs_list");
    if(songs){
        let song_ids = get_song_ids();
        call_ajax(song_ids);
    }
}

function call_ajax(song_ids){
    let data = {
        'action' : settings.action,
        'song_ids': song_ids
    };

    jQuery.post(settings.ajaxurl, data, function(response) {
        populate_list_with_data(response.data);
    });
}

function populate_list_with_data(songs_data){
    let songs = document.getElementById("exsultate_songs_list");
    if(songs){
        songs_data.forEach(function(currentValue, index){
            let list_element = list_item_from_song_data(currentValue);
            songs.appendChild(list_element);
        });
    }
}

function list_item_from_song_data(song_data){
    let list_element = document.createElement('li');
    list_element.setAttribute('id', song_data['id']);
    let link = document.createElement('a');
    link.setAttribute('href', song_data['url']);
    link.innerText = song_data['title'];
    list_element.appendChild(link);
    let button = document.createElement('button');
    button.setAttribute('onclick', 'remove_from_list('.concat(song_data['id']).concat(')'))
    button.setAttribute('style', "float:right;padding:5px 30px 4px;");
    button.innerText = 'Usuń';
    list_element.appendChild(button);
    return list_element;
}

function storage_object(){
    return window.localStorage;
}

function remove_from_list(song_id){
    let songs = get_song_ids();
    let index = 0;
    songs.forEach(song_item => {
        if(song_item['id'] === song_id){
            index = songs.indexOf(song_item);
        }
    });
    songs = songs.splice(index, 1);
    set_song_ids(songs);
    initialize_songs_list();
}

function generate_songbook(){
    let url = settings.resturl.concat(settings.songbook_rest_path);
    let parameters = '';
    let song_ids = get_song_ids();
    song_ids.forEach(function(item){
       parameters = parameters.concat(item).concat('+');
    });
    console.log(url.concat(parameters));
    window.open(url.concat(parameters));
}

document.addEventListener("DOMContentLoaded", function(){
    initialize_songs_list();
});