$( document ).ready(function() {

    const wikiUrl = 'https://en.wikipedia.org'
    const params = 'action=query&list=search&format=json&origin=*'

    new Autocomplete('#autocomplete', {
    search: input => {
        const url = `${base_url}codeigniter4/public/Search/global?${params}&srsearch=${encodeURI(input)}`

        return new Promise(resolve => {
        if (input.length < 3) {
            return resolve([])
        }

        fetch(url)
            .then(response => response.json())
            .then(data => {
                resolve(data);
            })
        })
    },

    renderResult: (result, props) => {
        var content = '';
        let group = ''
        if (result.index % 3 === 0) {
          group = `<li class="group">Group</li>`
        }
        if (result.type == 'player')
        {
            content = '<a href="' + base_url + 'codeigniter4/public/players/view/' + result.licence + '"><li><div class=""><img class="img-profile rounded-circle" style="height: 2rem; width: 2rem; margin-right: 5px;" src="'+ base_url + '/template/img/undraw_profile.svg">';
            content += group + '' + result.first_name + ' ' + result.last_name + '</div></li></a>';
        }
        if (result.type == 'club')
        {
            content = '<a href="' + base_url + 'codeigniter4/public/club/view/' + result.club_id + '">' + result.name + '</a>';
        }
      
        return content;
      },

    getResultValue: result => result.type,

    // Open the selected article in
    // a new window
    onSubmit: result => {
        window.open(`${wikiUrl}/wiki/${encodeURI(result.type)}`)
    },
    })
});