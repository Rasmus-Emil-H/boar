onmessage = async(e) => {
    const response = await fetch(e.data.url, {method: 'GET'});
    const jsonResponse = await response.json();
    postMessage(jsonResponse.responseJSON ?? '');
}