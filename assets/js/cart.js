function add_to_flyer(){
    let flyer_items = JSON.parse(storage_object().getItem("flyer_items"));
    if(!flyer_items){
        flyer_items = [];
    }
    let song_id = get_post_id();
    let list_contains = flyer_items.includes(song_id);
    if(!list_contains){
        flyer_items.push(song_id);
        storage_object().setItem("flyer_items", JSON.stringify(flyer_items));
    }
    console.log(flyer_items);
    //disable_add_to_cart_button();
}

function get_post_id(){
    let string_value =  document.getElementsByTagName("article")[0].id;
    return parseInt(string_value.split("-")[1]);
}

function clear_cart(){
    storage_object().setItem("flyer_items", JSON.stringify([]));
    initialize_songs_list();
}

function initialize_songs_list(){
    let songs = document.getElementById("exsultate_songs_list");
    if(songs){
        let song_ids = JSON.parse(storage_object().getItem("flyer_items"));
        if(!song_ids){
            song_ids = [];
        }
        console.log(song_ids);
        call_ajax(song_ids);
    }
}

function call_ajax(song_ids){
    let data = {
        'action' : settings.action,
        'song_ids': song_ids
    };

    //jQuery('#exsultate_songs_list').post(settings.ajaxurl, data, function(response) {
    jQuery.post(settings.ajaxurl, data, function(response) {

        console.log(response.data);
        populate_list_with_data(response);

    });
}

function populate_list_with_data(songs_data){

}

function storage_object(){
    return window.localStorage;
}

function song_list_item_from(song_item){
    let output = "<li id='".concat(song_item['id']).concat("'><a href='").concat(song_item['url']);
    output = output.concat("'>").concat(song_item['title']).concat("</a>");
    let button_html = "<button onclick=remove_from_list('".concat(song_item['id']).concat("') style='float:right;'>");
    button_html = button_html.concat("Usuń</button>");
    output = output.concat(button_html).concat("</li>");
    return output;
}

function remove_from_list(song_id){
    let songs = JSON.parse(storage_object().getItem("flyer_items"));
    let index = 0;
    songs.forEach(song_item => {
        if(song_item['id'] === song_id){
            index = songs.indexOf(song_item);
        }
    });
    songs = songs.splice(index, 1);
    storage_object().setItem("flyer_items", JSON.stringify(songs));
    initialize_songs_list();
}

document.addEventListener("DOMContentLoaded", function(){
    initialize_songs_list();
});