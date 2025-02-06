onmessage = async(e) => {
    const fd = new FormData();
    for (let key in e.data) {
        if (typeof e.data[key] === 'object') {
            row = 0;
            for (let iteration in e.data[key]) {
                fd.append(`${key.replace('[]', '')}${row}`, e.data[key][iteration]);
                row++;
            }
        } else {
            fd.append(key, e.data[key]);
        }
    };

    const response = await fetch(e.data.url ?? '/file', {method: "POST", body: fd});
    const jsonResponse = await response.json();
    postMessage(jsonResponse.responseJSON ?? '');
}