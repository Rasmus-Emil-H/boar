onmessage = async (e) => {
    try {
        const fd = new FormData();
        for (let key in e.data) fd.append(key, e.data[key]);

        const response = await fetch(e.data.url ?? '/file', {method: "POST", body: fd});
        const jsonResponse = await response.json();
        
        postMessage(jsonResponse.responseJSON ?? '');
    } catch (e) {
        console.log(e);
    }
}